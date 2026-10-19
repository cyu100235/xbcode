<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\api;

/**
 * 插件表结构检测与同步
 *
 * 以各插件 install.sql 为唯一权威方向，与数据库现有表结构比对：
 *   - 表不存在        → 按 install.sql 原始建表语句创建
 *   - 字段缺失        → ADD COLUMN
 *   - 字段定义不同    → MODIFY COLUMN
 *   - 数据库多余字段  → DROP COLUMN
 *   - 索引缺失/不同   → ADD / DROP + ADD
 *   - 索引多余        → DROP INDEX
 *   - 数据库多余表    → DROP TABLE（仅限带配置前缀的表，且仅在扫描全部插件时执行）
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class TableStructure
{
    /**
     * 整数家族类型（显示宽度在 MySQL 8.0+ 已废弃，不参与长度比对）
     */
    private const INT_TYPES = ['int', 'tinyint', 'smallint', 'mediumint', 'bigint'];

    /**
     * 无长度语义的类型（不参与长度比对）
     */
    private const NO_LENGTH_TYPES = [
        'text', 'tinytext', 'mediumtext', 'longtext',
        'blob', 'tinyblob', 'mediumblob', 'longblob',
        'json', 'date', 'datetime', 'timestamp', 'time', 'year',
    ];

    /**
     * install.sql 中可能出现的旧表前缀（按优先级）
     */
    private const OLD_PREFIXES = ['__PREFIX__', 'xb_', 'php_'];

    /**
     * 数据库现有表名（小写为键），惰性加载一次
     * @var array|null
     */
    private $dbTables = null;

    /**
     * 创建实例
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function make()
    {
        return new static;
    }

    /**
     * 执行表结构检测与同步
     * @param bool   $dryRun 仅检测输出差异SQL，不实际执行
     * @param string $plugin 仅处理指定插件（插件目录名），为空则处理全部插件
     * @return array 检测报告
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function run(bool $dryRun = false, string $plugin = ''): array
    {
        $prefix = (string)Mysql::getPrefix();
        $report = [
            'dry_run' => $dryRun,
            'tables' => [],
            'dropped_tables' => [],
            'errors' => [],
            'summary' => [
                'created' => 0,
                'synced' => 0,
                'ok' => 0,
                'dropped_tables' => 0,
                'errors' => 0,
            ],
        ];

        // 收集所有插件 install.sql 声明的表（以实际表名为键）
        $declared = $this->collectDeclaredTables($plugin, $prefix);

        // 预加载数据库现有表名
        $this->dbTables();

        // 逐表创建或同步
        foreach ($declared as $realName => $info) {
            try {
                if ($this->hasTable($realName)) {
                    $item = $this->syncTable($realName, $info, $dryRun);
                    $report['tables'][$realName] = $item;
                    if ($item['status'] === 'synced') {
                        $report['summary']['synced']++;
                    } else {
                        $report['summary']['ok']++;
                    }
                } else {
                    $sql = $info['create_sql_real'];
                    if (!$dryRun) {
                        Mysql::execute($sql);
                    }
                    $report['tables'][$realName] = [
                        'plugin' => $info['plugin'],
                        'status' => 'created',
                        'sql' => [$sql],
                        'columns' => ['added' => [], 'modified' => [], 'dropped' => []],
                        'indexes' => ['added' => [], 'modified' => [], 'dropped' => []],
                    ];
                    $report['summary']['created']++;
                }
            } catch (\Throwable $e) {
                $report['errors'][] = "{$realName}: {$e->getMessage()}";
                $report['summary']['errors']++;
            }
        }

        // 仅在全量模式下删除数据库中多余的（带前缀的）表，避免单插件模式误删其它插件表
        if ($plugin === '') {
            $this->dropExtraTables($declared, $prefix, $dryRun, $report);
        }

        return $report;
    }

    /**
     * 收集所有插件 install.sql 中声明的表结构
     * @param string $plugin 指定插件名，空则全部
     * @param string $prefix 数据库配置前缀
     * @return array 以实际表名为键的结构信息
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function collectDeclaredTables(string $plugin, string $prefix): array
    {
        $pluginDir = base_path() . '/plugin';
        $plugins = [];
        if ($plugin !== '') {
            $plugins = [$plugin];
        } else {
            foreach (glob($pluginDir . '/*', GLOB_ONLYDIR) ?: [] as $dir) {
                $plugins[] = basename($dir);
            }
        }

        $declared = [];
        foreach ($plugins as $name) {
            $file = "{$pluginDir}/{$name}/install.sql";
            if (!is_file($file)) {
                continue;
            }
            foreach ($this->parseInstallFile($file) as $table) {
                $rawName = $table['raw_name'];
                $realName = $this->resolveTableName($rawName, $prefix);
                // 同一张表被多个插件声明时以先声明者为准
                if (isset($declared[$realName])) {
                    continue;
                }
                $declared[$realName] = [
                    'plugin' => $name,
                    'sql_file' => $file,
                    'raw_name' => $rawName,
                    'create_sql_real' => $this->replaceTableName($table['create_sql'], $rawName, $realName),
                    'columns' => $table['columns'],
                    'indexes' => $table['indexes'],
                ];
            }
        }
        return $declared;
    }

    /**
     * 同步单张已存在的表
     * @param string $realName 实际表名
     * @param array  $info     声明信息
     * @param bool   $dryRun   是否仅检测
     * @return array 单表处理结果
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function syncTable(string $realName, array $info, bool $dryRun): array
    {
        $sqls = [];
        $colStat = ['added' => [], 'modified' => [], 'dropped' => []];
        $idxStat = ['added' => [], 'modified' => [], 'dropped' => []];

        $dbCols = $this->getDbColumns($realName);
        $fileCols = $info['columns'];
        $fileMap = [];
        foreach ($fileCols as $col) {
            $fileMap[strtolower($col['name'])] = $col;
        }

        // 新增缺失字段 / 修改定义不同的字段
        $prevName = null;
        foreach ($fileCols as $index => $col) {
            $lower = strtolower($col['name']);
            $def = $this->buildColumnDefinition($col);
            if (!isset($dbCols[$lower])) {
                $position = '';
                if ($index === 0) {
                    $position = ' FIRST';
                } elseif ($prevName !== null) {
                    $position = " AFTER `{$prevName}`";
                }
                $sqls[] = "ALTER TABLE `{$realName}` ADD COLUMN {$def}{$position}";
                $colStat['added'][] = $col['name'];
            } elseif ($this->isColumnChanged($col, $dbCols[$lower])) {
                $sqls[] = "ALTER TABLE `{$realName}` MODIFY COLUMN {$def}";
                $colStat['modified'][] = $col['name'];
            }
            $prevName = $col['name'];
        }

        // 删除数据库多余字段（跳过自增列，避免破坏主键）
        foreach ($dbCols as $lower => $dbCol) {
            if (isset($fileMap[$lower])) {
                continue;
            }
            if (!empty($dbCol['is_auto'])) {
                continue;
            }
            $sqls[] = "ALTER TABLE `{$realName}` DROP COLUMN `{$dbCol['name']}`";
            $colStat['dropped'][] = $dbCol['name'];
        }

        // 索引同步
        $dbIdx = $this->getDbIndexes($realName);
        $fileIdx = $info['indexes'];
        foreach ($fileIdx as $key => $fi) {
            if (!isset($dbIdx[$key])) {
                $sqls[] = $this->buildAddIndexSql($realName, $fi);
                $idxStat['added'][] = $fi['raw_name'] ?: $key;
            } elseif ($this->isIndexChanged($fi, $dbIdx[$key])) {
                $sqls[] = $this->buildDropIndexSql($realName, $dbIdx[$key]);
                $sqls[] = $this->buildAddIndexSql($realName, $fi);
                $idxStat['modified'][] = $fi['raw_name'] ?: $key;
            }
        }
        foreach ($dbIdx as $key => $di) {
            // 不与主键较劲：文件中未声明主键时不删除现有主键
            if ($key === 'primary') {
                continue;
            }
            if (!isset($fileIdx[$key])) {
                $sqls[] = $this->buildDropIndexSql($realName, $di);
                $idxStat['dropped'][] = $di['raw_name'] ?: $key;
            }
        }

        // 执行
        if (!$dryRun) {
            foreach ($sqls as $sql) {
                Mysql::execute($sql);
            }
        }

        return [
            'plugin' => $info['plugin'],
            'status' => empty($sqls) ? 'ok' : 'synced',
            'sql' => $sqls,
            'columns' => $colStat,
            'indexes' => $idxStat,
        ];
    }

    /**
     * 删除数据库中多余的、带配置前缀的表
     * @param array  $declared 已声明表（以实际表名为键）
     * @param string $prefix   配置前缀
     * @param bool   $dryRun   是否仅检测
     * @param array  $report   报告引用
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function dropExtraTables(array $declared, string $prefix, bool $dryRun, array &$report): void
    {
        if ($prefix === '') {
            return;
        }
        $declaredLower = [];
        foreach (array_keys($declared) as $name) {
            $declaredLower[strtolower($name)] = true;
        }
        $prefixLower = strtolower($prefix);
        foreach ($this->dbTables() as $table) {
            // 仅管理带配置前缀的表
            if (!str_starts_with(strtolower($table), $prefixLower)) {
                continue;
            }
            if (isset($declaredLower[strtolower($table)])) {
                continue;
            }
            if (!$dryRun) {
                Mysql::dropTable($table);
            }
            $report['dropped_tables'][] = $table;
            $report['summary']['dropped_tables']++;
        }
    }

    /**
     * 判断表是否存在（大小写不敏感）
     * @param string $table 完整表名
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function hasTable(string $table): bool
    {
        return isset($this->dbTables()[strtolower($table)]);
    }

    /**
     * 加载数据库现有表名（小写为键）
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function dbTables(): array
    {
        if ($this->dbTables === null) {
            $this->dbTables = [];
            foreach (Mysql::getTableNames() as $name) {
                $this->dbTables[strtolower($name)] = $name;
            }
        }
        return $this->dbTables;
    }

    /**
     * 解析 install.sql 文件中的所有 CREATE TABLE 结构
     * @param string $file 文件路径
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function parseInstallFile(string $file): array
    {
        $content = @file_get_contents($file);
        if ($content === false || trim($content) === '') {
            return [];
        }
        $clean = $this->stripComments($content);

        preg_match_all(
            '/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?`?([A-Za-z0-9_]+)`?\s*\(/i',
            $clean,
            $matches,
            PREG_OFFSET_CAPTURE
        );

        $result = [];
        foreach ($matches[0] as $i => $whole) {
            $wholeStr = $whole[0];
            $offset = $whole[1];
            $rawName = $matches[1][$i][0];
            // CREATE 语句中左括号的位置
            $openPos = $offset + strlen($wholeStr) - 1;
            $closePos = $this->matchParen($clean, $openPos);
            if ($closePos === null) {
                continue;
            }
            $endPos = $this->findStatementEnd($clean, $closePos);
            $createSql = substr($clean, $offset, $endPos - $offset + 1);
            $inner = substr($clean, $openPos + 1, $closePos - $openPos - 1);

            $parsed = $this->parseDefinitions($this->splitTopLevel($inner));
            $result[] = [
                'raw_name' => $rawName,
                'create_sql' => trim($createSql),
                'columns' => $parsed['columns'],
                'indexes' => $parsed['indexes'],
            ];
        }
        return $result;
    }

    /**
     * 去除 SQL 注释（行注释与块注释），引号内的注释符号不受影响
     * @param string $content
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function stripComments(string $content): string
    {
        $out = '';
        $len = strlen($content);
        $inStr = false;
        $quote = '';
        for ($i = 0; $i < $len; $i++) {
            $ch = $content[$i];
            if ($inStr) {
                $out .= $ch;
                if ($ch === '\\') {
                    if ($i + 1 < $len) {
                        $out .= $content[++$i];
                    }
                    continue;
                }
                if ($ch === $quote) {
                    $inStr = false;
                }
                continue;
            }
            if ($ch === "'" || $ch === '"' || $ch === '`') {
                $inStr = true;
                $quote = $ch;
                $out .= $ch;
                continue;
            }
            // 块注释 /* ... */
            if ($ch === '/' && ($content[$i + 1] ?? '') === '*') {
                $end = strpos($content, '*/', $i + 2);
                $i = $end === false ? $len : $end + 1;
                continue;
            }
            // 行注释 -- （需后接空白或行尾）
            if ($ch === '-' && ($content[$i + 1] ?? '') === '-'
                && (($content[$i + 2] ?? "\n") === ' ' || ($content[$i + 2] ?? "\n") === "\t" || ($content[$i + 2] ?? "\n") === "\n" || ($content[$i + 2] ?? "\n") === "\r")) {
                $end = strpos($content, "\n", $i);
                $i = $end === false ? $len : $end - 1;
                continue;
            }
            // 行注释 #
            if ($ch === '#') {
                $end = strpos($content, "\n", $i);
                $i = $end === false ? $len : $end - 1;
                continue;
            }
            $out .= $ch;
        }
        return $out;
    }

    /**
     * 从指定左括号位置匹配对应的右括号
     * @param string $s
     * @param int    $openPos
     * @return int|null
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function matchParen(string $s, int $openPos): ?int
    {
        $depth = 0;
        $len = strlen($s);
        $inStr = false;
        $quote = '';
        for ($i = $openPos; $i < $len; $i++) {
            $ch = $s[$i];
            if ($inStr) {
                if ($ch === '\\') {
                    $i++;
                    continue;
                }
                if ($ch === $quote) {
                    $inStr = false;
                }
                continue;
            }
            if ($ch === "'" || $ch === '"' || $ch === '`') {
                $inStr = true;
                $quote = $ch;
                continue;
            }
            if ($ch === '(') {
                $depth++;
            } elseif ($ch === ')') {
                $depth--;
                if ($depth === 0) {
                    return $i;
                }
            }
        }
        return null;
    }

    /**
     * 查找语句结束位置（分号，忽略字符串中的分号）
     * @param string $s
     * @param int    $from
     * @return int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function findStatementEnd(string $s, int $from): int
    {
        $len = strlen($s);
        $inStr = false;
        $quote = '';
        for ($i = $from; $i < $len; $i++) {
            $ch = $s[$i];
            if ($inStr) {
                if ($ch === '\\') {
                    $i++;
                    continue;
                }
                if ($ch === $quote) {
                    $inStr = false;
                }
                continue;
            }
            if ($ch === "'" || $ch === '"' || $ch === '`') {
                $inStr = true;
                $quote = $ch;
                continue;
            }
            if ($ch === ';') {
                return $i;
            }
        }
        return $len - 1;
    }

    /**
     * 按顶层逗号拆分（忽略括号与引号内的逗号）
     * @param string $s
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function splitTopLevel(string $s): array
    {
        $parts = [];
        $buf = '';
        $depth = 0;
        $inStr = false;
        $quote = '';
        $len = strlen($s);
        for ($i = 0; $i < $len; $i++) {
            $ch = $s[$i];
            if ($inStr) {
                $buf .= $ch;
                if ($ch === '\\') {
                    if ($i + 1 < $len) {
                        $buf .= $s[++$i];
                    }
                    continue;
                }
                if ($ch === $quote) {
                    $inStr = false;
                }
                continue;
            }
            if ($ch === "'" || $ch === '"' || $ch === '`') {
                $inStr = true;
                $quote = $ch;
                $buf .= $ch;
                continue;
            }
            if ($ch === '(') {
                $depth++;
                $buf .= $ch;
                continue;
            }
            if ($ch === ')') {
                $depth--;
                $buf .= $ch;
                continue;
            }
            if ($ch === ',' && $depth === 0) {
                $parts[] = $buf;
                $buf = '';
                continue;
            }
            $buf .= $ch;
        }
        if (trim($buf) !== '') {
            $parts[] = $buf;
        }
        return $parts;
    }

    /**
     * 解析字段定义与索引定义
     * @param array $defs 顶层定义片段
     * @return array ['columns'=>[], 'indexes'=>[]]
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function parseDefinitions(array $defs): array
    {
        $columns = [];
        $indexes = [];
        foreach ($defs as $def) {
            $def = trim($def);
            if ($def === '') {
                continue;
            }
            // 字段行：`name` type[(...)] [...]
            if (preg_match('/^`([^`]+)`\s+([A-Za-z][A-Za-z0-9_]*)\s*(?:\(([^)]*)\))?\s*(.*)$/s', $def, $m)) {
                $columns[] = $this->parseColumn($m[1], $m[2], $m[3] ?? null, $m[4] ?? '');
                continue;
            }
            // 主键
            if (preg_match('/^PRIMARY\s+KEY\s*\((.+)\)\s*(?:USING\s+\w+)?\s*$/is', $def, $m)) {
                $indexes['primary'] = [
                    'raw_name' => 'PRIMARY',
                    'unique' => true,
                    'columns' => $this->parseIndexColumns($m[1]),
                ];
                continue;
            }
            // 唯一键 / 普通索引（带名称）
            if (preg_match('/^(UNIQUE\s+)?(?:KEY|INDEX)\s+`?([^`\s(]+)`?\s*\((.+)\)\s*(?:USING\s+\w+)?\s*$/is', $def, $m)) {
                $unique = trim($m[1]) !== '';
                $name = $m[2];
                $indexes[strtolower($name)] = [
                    'raw_name' => $name,
                    'unique' => $unique,
                    'columns' => $this->parseIndexColumns($m[3]),
                ];
                continue;
            }
            // UNIQUE(name) 无名唯一索引
            if (preg_match('/^UNIQUE\s+`?([^`\s(]+)`?\s*\((.+)\)\s*(?:USING\s+\w+)?\s*$/is', $def, $m)) {
                $cols = $this->parseIndexColumns($m[2]);
                $name = $m[1] !== '' ? $m[1] : ($cols[0] ?? 'unique');
                $indexes[strtolower($name)] = [
                    'raw_name' => $name,
                    'unique' => true,
                    'columns' => $cols,
                ];
                continue;
            }
        }
        return ['columns' => $columns, 'indexes' => $indexes];
    }

    /**
     * 解析单个字段定义
     * @param string      $name  字段名
     * @param string      $type  字段类型
     * @param string|null $rawLen 类型括号内内容
     * @param string      $rest  其余属性串
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function parseColumn(string $name, string $type, ?string $rawLen, string $rest): array
    {
        $type = strtolower($type);
        $rawLen = $rawLen !== null ? trim($rawLen) : null;

        $enumRaw = null;
        $enumValues = null;
        $length = null;
        if ($rawLen !== null && $rawLen !== '') {
            if (in_array($type, ['enum', 'set'], true)) {
                $enumRaw = $rawLen;
                $enumValues = Mysql::normalizeEnumValues($rawLen);
            } else {
                // 移除空格，便于 decimal(10, 2) 之类的比对
                $length = str_replace(' ', '', $rawLen);
            }
        }

        $unsigned = (bool)preg_match('/\bUNSIGNED\b/i', $rest);
        $charset = null;
        if (preg_match('/\bCHARACTER\s+SET\s+([A-Za-z0-9_]+)/i', $rest, $csm)) {
            $charset = $csm[1];
        }
        $collate = null;
        if (preg_match('/\bCOLLATE\s+([A-Za-z0-9_]+)/i', $rest, $clm)) {
            $collate = $clm[1];
        }
        $isNull = !preg_match('/\bNOT\s+NULL\b/i', $rest);
        $isAuto = (bool)preg_match('/\bAUTO_INCREMENT\b/i', $rest);

        $onUpdate = null;
        if (preg_match('/\bON\s+UPDATE\s+([A-Za-z_]+(?:\(\))?)/i', $rest, $oum)) {
            $onUpdate = $this->normalizeKeyword($oum[1]);
        }

        // 默认值
        $defaultSql = null;
        $defaultCmp = null;
        if (preg_match("/\bDEFAULT\s+(?:'((?:[^'\\\\]|\\\\.)*)'|(\S+))/i", $rest, $dm)) {
            if (preg_match("/\bDEFAULT\s+'/i", $dm[0])) {
                // 字符串默认值（可能为空字符串）
                $val = $dm[1] ?? '';
                $defaultSql = "'" . addslashes($val) . "'";
                $defaultCmp = $val;
            } else {
                // 数字/关键字默认值
                $token = $dm[2] ?? '';
                $norm = $this->normalizeKeyword($token);
                if ($norm !== null) {
                    $defaultSql = $token;
                    $defaultCmp = $this->normalizeDefault($token);
                }
            }
        }

        $comment = null;
        if (preg_match("/\bCOMMENT\s+'((?:[^'\\\\]|\\\\.)*)'/i", $rest, $cm)) {
            $comment = stripslashes($cm[1]);
        }

        return [
            'name' => $name,
            'type' => $type,
            'length' => $length,
            'unsigned' => $unsigned,
            'enum_raw' => $enumRaw,
            'enum_values' => $enumValues,
            'charset' => $charset,
            'collate' => $collate,
            'is_null' => $isNull,
            'is_auto' => $isAuto,
            'on_update' => $onUpdate,
            'default_sql' => $defaultSql,
            'default' => $defaultCmp,
            'comment' => $comment,
        ];
    }

    /**
     * 解析索引列列表
     * @param string $list
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function parseIndexColumns(string $list): array
    {
        $cols = [];
        foreach ($this->splitTopLevel($list) as $c) {
            $c = trim($c);
            // 去掉前缀长度，如 `name`(10)
            $c = preg_replace('/\(\d+\)\s*$/', '', $c);
            $c = str_replace('`', '', $c);
            $c = preg_replace('/\s+(ASC|DESC)$/i', '', $c);
            $c = strtolower(trim($c));
            if ($c !== '') {
                $cols[] = $c;
            }
        }
        return $cols;
    }

    /**
     * 获取数据库字段（以字段名小写为键）
     * @param string $table
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function getDbColumns(string $table): array
    {
        $safe = addslashes($table);
        $rows = Mysql::query(
            "SELECT COLUMN_NAME, DATA_TYPE, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, COLUMN_COMMENT, EXTRA, "
            . "CHARACTER_MAXIMUM_LENGTH, NUMERIC_PRECISION, NUMERIC_SCALE "
            . "FROM INFORMATION_SCHEMA.COLUMNS "
            . "WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$safe}' "
            . "ORDER BY ORDINAL_POSITION"
        );
        $map = [];
        foreach ($rows as $row) {
            $type = strtolower($row['DATA_TYPE']);
            $length = null;
            if (in_array($type, self::INT_TYPES, true)) {
                $length = null;
            } elseif (in_array($type, ['decimal', 'numeric', 'float', 'double'], true)) {
                $precision = $row['NUMERIC_PRECISION'];
                $scale = $row['NUMERIC_SCALE'];
                $length = ($precision !== null && $scale !== null) ? "{$precision},{$scale}" : ($precision !== null ? (string)$precision : null);
            } else {
                $length = $row['CHARACTER_MAXIMUM_LENGTH'] !== null ? (string)$row['CHARACTER_MAXIMUM_LENGTH'] : null;
            }

            $enumValues = null;
            if (in_array($type, ['enum', 'set'], true)) {
                if (preg_match('/^(?:enum|set)\((.+)\)$/is', $row['COLUMN_TYPE'], $ev)) {
                    $enumValues = Mysql::normalizeEnumValues($ev[1]);
                }
            }

            $extra = (string)$row['EXTRA'];
            $onUpdate = null;
            if (preg_match('/on update (current_timestamp(?:\(\))?|now\(\))/i', $extra, $oum)) {
                $onUpdate = 'CURRENT_TIMESTAMP';
            }

            $map[strtolower($row['COLUMN_NAME'])] = [
                'name' => $row['COLUMN_NAME'],
                'type' => $type,
                'length' => $length,
                'unsigned' => stripos((string)$row['COLUMN_TYPE'], 'unsigned') !== false,
                'enum_values' => $enumValues,
                'is_null' => $row['IS_NULLABLE'] === 'YES',
                'is_auto' => stripos($extra, 'auto_increment') !== false,
                'on_update' => $onUpdate,
                'default' => $this->normalizeDefault($row['COLUMN_DEFAULT']),
                'comment' => (string)$row['COLUMN_COMMENT'],
            ];
        }
        return $map;
    }

    /**
     * 获取数据库索引（以索引名小写为键）
     * @param string $table
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function getDbIndexes(string $table): array
    {
        $rows = Mysql::query("SHOW INDEX FROM `{$table}`");
        $map = [];
        foreach ($rows as $row) {
            $key = strtolower($row['Key_name']);
            if (!isset($map[$key])) {
                $map[$key] = [
                    'raw_name' => $row['Key_name'],
                    'unique' => ((int)$row['Non_unique'] === 0),
                    'columns' => [],
                ];
            }
            $seq = (int)$row['Seq_in_index'];
            $map[$key]['columns'][$seq] = strtolower($row['Column_name']);
        }
        foreach ($map as $key => $item) {
            ksort($item['columns']);
            $map[$key]['columns'] = array_values($item['columns']);
        }
        return $map;
    }

    /**
     * 判断字段定义是否发生变化
     * @param array $fileCol
     * @param array $dbCol
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function isColumnChanged(array $fileCol, array $dbCol): bool
    {
        if ($fileCol['type'] !== $dbCol['type']) {
            return true;
        }
        // 长度（整数家族与无长度类型跳过）
        if (!in_array($fileCol['type'], self::INT_TYPES, true)
            && !in_array($fileCol['type'], self::NO_LENGTH_TYPES, true)
            && !in_array($fileCol['type'], ['enum', 'set'], true)) {
            $fileLen = $fileCol['length'] !== null ? (string)$fileCol['length'] : null;
            $dbLen = $dbCol['length'] !== null ? (string)$dbCol['length'] : null;
            if ($fileLen !== null && $fileLen !== $dbLen) {
                return true;
            }
        }
        if (in_array($fileCol['type'], ['enum', 'set'], true)) {
            $fileEnum = $fileCol['enum_values'] ?? [];
            $dbEnum = $dbCol['enum_values'] ?? [];
            if ($fileEnum !== $dbEnum) {
                return true;
            }
        }
        if ((bool)$fileCol['unsigned'] !== (bool)$dbCol['unsigned']) {
            return true;
        }
        if ((bool)$fileCol['is_null'] !== (bool)$dbCol['is_null']) {
            return true;
        }
        if ((bool)$fileCol['is_auto'] !== (bool)$dbCol['is_auto']) {
            return true;
        }
        if ((string)($fileCol['on_update'] ?? '') !== (string)($dbCol['on_update'] ?? '')) {
            return true;
        }
        if ($this->normalizeDefault($fileCol['default']) !== $this->normalizeDefault($dbCol['default'])) {
            return true;
        }
        if ((string)($fileCol['comment'] ?? '') !== (string)($dbCol['comment'] ?? '')) {
            return true;
        }
        return false;
    }

    /**
     * 判断索引是否发生变化
     * @param array $fileIdx
     * @param array $dbIdx
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function isIndexChanged(array $fileIdx, array $dbIdx): bool
    {
        if ((bool)$fileIdx['unique'] !== (bool)$dbIdx['unique']) {
            return true;
        }
        return array_values($fileIdx['columns']) !== array_values($dbIdx['columns']);
    }

    /**
     * 生成字段定义片段
     * @param array $col
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function buildColumnDefinition(array $col): string
    {
        $def = "`{$col['name']}` " . strtoupper($col['type']);
        if (in_array($col['type'], ['enum', 'set'], true)) {
            $def .= '(' . $col['enum_raw'] . ')';
        } elseif ($col['length'] !== null
            && !in_array($col['type'], self::INT_TYPES, true)
            && !in_array($col['type'], self::NO_LENGTH_TYPES, true)) {
            $def .= '(' . $col['length'] . ')';
        }
        if ($col['unsigned']) {
            $def .= ' UNSIGNED';
        }
        if (!empty($col['charset'])) {
            $def .= ' CHARACTER SET ' . $col['charset'];
        }
        if (!empty($col['collate'])) {
            $def .= ' COLLATE ' . $col['collate'];
        }
        $def .= $col['is_null'] ? ' NULL' : ' NOT NULL';
        if ($col['default_sql'] !== null) {
            $def .= ' DEFAULT ' . $col['default_sql'];
        }
        if (!empty($col['on_update'])) {
            $def .= ' ON UPDATE ' . $col['on_update'];
        }
        if ($col['is_auto']) {
            $def .= ' AUTO_INCREMENT';
        }
        if ($col['comment'] !== null && $col['comment'] !== '') {
            $def .= " COMMENT '" . addslashes($col['comment']) . "'";
        }
        return $def;
    }

    /**
     * 生成添加索引的 SQL
     * @param string $table
     * @param array  $idx
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function buildAddIndexSql(string $table, array $idx): string
    {
        $cols = '`' . implode('`, `', $idx['columns']) . '`';
        if (strtolower($idx['raw_name'] ?? '') === 'primary') {
            return "ALTER TABLE `{$table}` ADD PRIMARY KEY ({$cols})";
        }
        $unique = $idx['unique'] ? 'UNIQUE ' : '';
        $name = $idx['raw_name'];
        return "ALTER TABLE `{$table}` ADD {$unique}KEY `{$name}` ({$cols})";
    }

    /**
     * 生成删除索引的 SQL
     * @param string $table
     * @param array  $idx
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function buildDropIndexSql(string $table, array $idx): string
    {
        if (strtolower($idx['raw_name'] ?? '') === 'primary') {
            return "ALTER TABLE `{$table}` DROP PRIMARY KEY";
        }
        return "ALTER TABLE `{$table}` DROP INDEX `{$idx['raw_name']}`";
    }

    /**
     * 将文件中的表名解析为实际表名（替换旧前缀）
     * @param string $tableName
     * @param string $prefix
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function resolveTableName(string $tableName, string $prefix): string
    {
        $tableName = trim($tableName, '`');
        foreach (self::OLD_PREFIXES as $old) {
            if (str_starts_with($tableName, $old)) {
                return $prefix . substr($tableName, strlen($old));
            }
        }
        return $tableName;
    }

    /**
     * 替换 CREATE 语句中的原始表名为实际表名
     * @param string $createSql
     * @param string $rawName
     * @param string $realName
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function replaceTableName(string $createSql, string $rawName, string $realName): string
    {
        $pattern = '/^(\s*CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?)`?' . preg_quote($rawName, '/') . '`?/i';
        $replaced = preg_replace($pattern, '$1`' . $realName . '`', $createSql, 1);
        return $replaced !== null ? $replaced : $createSql;
    }

    /**
     * 标准化默认值（用于比对）
     * @param mixed $value
     * @return string|null
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function normalizeDefault($value): ?string
    {
        if ($value === null) {
            return null;
        }
        $s = trim((string)$value);
        // 去掉成对引号
        if (strlen($s) >= 2 && $s[0] === "'" && substr($s, -1) === "'") {
            $s = stripslashes(substr($s, 1, -1));
        }
        $upper = strtoupper($s);
        if ($upper === 'NULL') {
            return null;
        }
        if (in_array($upper, ['CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP()', 'NOW()', 'LOCALTIME', 'LOCALTIME()', 'LOCALTIMESTAMP', 'LOCALTIMESTAMP()'], true)) {
            return 'CURRENT_TIMESTAMP';
        }
        return $s;
    }

    /**
     * 标准化关键字（NULL 返回 null）
     * @param string $token
     * @return string|null
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function normalizeKeyword(string $token): ?string
    {
        $upper = strtoupper(trim($token));
        if ($upper === 'NULL') {
            return null;
        }
        if (in_array($upper, ['CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP()', 'NOW()', 'LOCALTIME', 'LOCALTIME()', 'LOCALTIMESTAMP', 'LOCALTIMESTAMP()'], true)) {
            return 'CURRENT_TIMESTAMP';
        }
        return $token;
    }
}
