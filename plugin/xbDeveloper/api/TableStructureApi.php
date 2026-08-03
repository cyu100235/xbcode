<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @version  1.0.0
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbDeveloper\api;

use plugin\xbCode\api\Mysql;

/**
 * 插件表结构变更检测
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class TableStructureApi
{
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
     * 获取插件表结构变动SQL
     * 以数据库当前结构为基准，对比 install.sql，生成使文件追上数据库现状所需的变更语句
     * @param string $name 插件标识
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getPackageSqlChange(string $name): string
    {
        $sqlFile = base_path() . "/plugin/{$name}/install.sql";
        if (!file_exists($sqlFile)) {
            return '';
        }
        // 获取数据库表前缀
        $dbConfig = Mysql::getConfig();
        $prefix   = $dbConfig['connections']['mysql']['prefix'] ?? 'xb_';
        // 解析 SQL 文件中所有 CREATE TABLE 定义
        $fileTables = $this->parseSqlFileTables($sqlFile);
        if (empty($fileTables)) {
            return '';
        }
        $changeSqls = [];
        foreach ($fileTables as $rawTableName => $fileColumns) {
            // 将 SQL 文件中的表名映射为实际表名（处理 __PREFIX__/xb_ 前缀）
            $realTableName = $this->resolveTableName($rawTableName, $prefix);
            // 表在数据库中不存在 → 表已被删除，跳过
            if (!Mysql::hasTable($realTableName)) {
                continue;
            }
            // 表已存在 → 以数据库为基准进行比对
            $dbColumns = Mysql::getColumns($realTableName);
            $dbColumnMap = [];
            foreach ($dbColumns as $col) {
                $dbColumnMap[strtolower($col['name'])] = $col;
            }
            $fileColumnMap = [];
            foreach ($fileColumns as $col) {
                $fileColumnMap[strtolower($col['name'])] = $col;
            }
            // 数据库有、文件无 → 数据库新增了字段，生成 ADD COLUMN（以数据库定义为准）
            foreach ($dbColumnMap as $colName => $dbCol) {
                if (!isset($fileColumnMap[$colName])) {
                    $changeSqls[] = $this->buildAddColumnSql($realTableName, $dbCol);
                }
            }
            // 数据库有、文件有，但定义不同 → 数据库修改了字段，生成 MODIFY COLUMN（以数据库定义为准）
            foreach ($dbColumnMap as $colName => $dbCol) {
                if (!isset($fileColumnMap[$colName])) {
                    continue;
                }
                $fileCol = $fileColumnMap[$colName];
                if ($this->isColumnChanged($fileCol, $dbCol)) {
                    $changeSqls[] = $this->buildModifyColumnSql($realTableName, $dbCol);
                }
            }
            // 数据库无、文件有 → 数据库删除了字段，跳过（安全考虑）
        }
        return implode('', $changeSqls);
    }

    /**
     * 判断两个字段定义是否存在差异
     * @param array $fileCol 文件中的字段定义
     * @param array $dbCol   数据库中的字段定义
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function isColumnChanged(array $fileCol, array $dbCol): bool
    {
        // 整数类型集合（显示宽度在 MySQL 8.0+ 已废弃，不参与比对）
        $intTypes = ['int', 'tinyint', 'smallint', 'mediumint', 'bigint'];
        // 比较类型
        if (strtolower($fileCol['type']) !== strtolower($dbCol['type'])) {
            return true;
        }
        // 比较长度（整数类型跳过，避免 INT(11) vs NUMERIC_PRECISION=10 误判）
        if (!in_array(strtolower($fileCol['type']), $intTypes)) {
            $fileLen = $fileCol['length'] !== null ? (string)$fileCol['length'] : null;
            $dbLen   = $dbCol['length']   !== null ? (string)$dbCol['length']   : null;
            if ($fileLen !== null && $fileLen !== $dbLen) {
                return true;
            }
        }
        // 比较 enum/set 枚举局列表
        if (in_array(strtolower($fileCol['type']), ['enum', 'set'])) {
            $fileEnum = $fileCol['enum_values'] ?? [];
            $dbEnum   = $dbCol['enum_values']   ?? [];
            if ($fileEnum !== $dbEnum) {
                return true;
            }
        }
        // 比较 NULL 属性
        if ((bool)$fileCol['is_null'] !== (bool)$dbCol['is_null']) {
            return true;
        }
        // 比较默认値（文件中有明确默认値才比对）
        if (array_key_exists('default', $fileCol) && $fileCol['default'] !== null) {
            $dbDefault = isset($dbCol['default']) ? (string)$dbCol['default'] : null;
            if ((string)$fileCol['default'] !== $dbDefault) {
                return true;
            }
        }
        // 比较字段注释（文件中有注释才比对）
        if ($fileCol['comment'] !== null) {
            $dbComment = $dbCol['comment'] ?? '';
            if ($fileCol['comment'] !== $dbComment) {
                return true;
            }
        }
        return false;
    }

    /**
     * 生成字段类型定义片段（type + length + null + comment）
     * @param array $col 字段定义
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function buildColumnDefinition(array $col): string
    {
        $intTypes = ['int', 'tinyint', 'smallint', 'mediumint', 'bigint'];
        $colType  = strtolower($col['type']);
        $def      = strtoupper($col['type']);
        if (in_array($colType, ['enum', 'set'])) {
            // enum/set：优先使用数据库返回的原始局列表字符串
            // dbCol 的 enum_values 已经是标准化数组，需重新拼为 SQL 字符串
            $values = array_map(fn($v) => "'" . addslashes($v) . "'", $col['enum_values'] ?? []);
            $def   .= '(' . implode(',', $values) . ')';
        } elseif ($col['length'] !== null && $col['length'] !== '' && !in_array($colType, $intTypes)) {
            // 整数类型不带显示宽度（MySQL 8.0+ 已废弃）
            $def .= "({$col['length']})";
        }
        $def .= $col['is_null'] ? ' NULL' : ' NOT NULL';
        // 带入默认値
        if (isset($col['default']) && $col['default'] !== null) {
            $numericTypes = array_merge($intTypes, ['decimal', 'numeric', 'float', 'double']);
            if (in_array($colType, $numericTypes)) {
                $def .= " DEFAULT {$col['default']}";
            } else {
                $escaped = addslashes($col['default']);
                $def .= " DEFAULT '{$escaped}'";
            }
        }
        if (!empty($col['comment'])) {
            $escaped = addslashes($col['comment']);
            $def .= " COMMENT '{$escaped}'";
        }
        return $def;
    }

    /**
     * 生成 ADD COLUMN SQL 语句
     * @param string $tableName 完整表名
     * @param array  $col       字段定义
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function buildAddColumnSql(string $tableName, array $col): string
    {
        $def = $this->buildColumnDefinition($col);
        return "ALTER TABLE `{$tableName}` ADD COLUMN `{$col['name']}` {$def};\n";
    }

    /**
     * 生成 MODIFY COLUMN SQL 语句
     * @param string $tableName 完整表名
     * @param array  $col       字段定义
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function buildModifyColumnSql(string $tableName, array $col): string
    {
        $def = $this->buildColumnDefinition($col);
        // 如果是字符串类型字段，添加字符集设置
        $charTypes = ['varchar', 'char', 'text', 'tinytext', 'mediumtext', 'longtext'];
        $colType = strtolower($col['type']);
        if (in_array($colType, $charTypes)) {
            $def .= " CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
        }
        return "ALTER TABLE `{$tableName}` MODIFY COLUMN `{$col['name']}` {$def};\n";
    }

    /**
     * 解析 SQL 文件中所有 CREATE TABLE 的表结构
     * @param string $sqlFile SQL 文件路径
     * @return array 以表名为键，字段信息数组为值
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function parseSqlFileTables(string $sqlFile): array
    {
        $content = file_get_contents($sqlFile);
        if ($content === false) {
            return [];
        }
        // 移除单行注释
        $content = preg_replace('/^--.*$/m', '', $content);
        // 匹配所有 CREATE TABLE 块（贪婪到分号结束）
        $pattern = '/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?`?([\w]+)`?\s*\(([\s\S]*?)\)\s*(?:ENGINE[^;]*)?;/i';
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
        $tables = [];
        foreach ($matches as $match) {
            $tableName = trim($match[1]);
            $colBlock  = $match[2];
            $columns   = $this->parseColumnBlock($colBlock);
            $tables[$tableName] = $columns;
        }
        return $tables;
    }

    /**
     * 解析 CREATE TABLE 内的字段定义块
     * @param string $block 括号内的字段定义字符串
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function parseColumnBlock(string $block): array
    {
        $lines   = explode("\n", $block);
        $columns = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }
            // 跳过约束定义行（PRIMARY KEY, KEY, UNIQUE, INDEX, CONSTRAINT）
            if (preg_match('/^(PRIMARY\s+KEY|KEY|UNIQUE|INDEX|CONSTRAINT|FOREIGN)/i', $line)) {
                continue;
            }
            // 匹配字段行：`column_name` type[(length)] [...]
            if (!preg_match('/^`([\w]+)`\s+(\w+)(?:\(([^)]+)\))?(.*)$/i', $line, $m)) {
                continue;
            }
            $colName = $m[1];
            $colType = strtolower($m[2]);
            $rawLen  = isset($m[3]) ? trim($m[3]) : null;
            $rest    = isset($m[4]) ? $m[4] : '';
            // 处理字段长度与枚举局
            $length     = null;
            $enumValues = null;
            if ($rawLen !== null && $rawLen !== '') {
                if (in_array($colType, ['enum', 'set'])) {
                    // enum/set：保存局列表用于对比，不存入 length
                    $enumValues = Mysql::normalizeEnumValues($rawLen);
                } else {
                    // 其他类型（decimal 等多精度类型）保留原始字符串
                    $length = $rawLen;
                }
            }
            // 是否允许 NULL
            $isNull = !str_contains(strtoupper($rest), 'NOT NULL');
            // 提取字段默认値 DEFAULT '...' 或 DEFAULT 数字
            $default = null;
            if (preg_match("/DEFAULT\\s+'((?:[^'\\\\]|\\\\.)*)'(?:\\s|,|$)/i", $rest, $dm)) {
                // 字符串默认値
                $default = stripslashes($dm[1]);
            } elseif (preg_match('/DEFAULT\s+(\S+)/i', $rest, $dm)) {
                // 数字/关键字默认値（NULL/CURRENT_TIMESTAMP/0/1 等）
                $raw = strtoupper(trim($dm[1], ','));
                if ($raw !== 'NULL') {
                    $default = trim($dm[1], ',');
                }
            }
            // 提取字段注释 COMMENT '...'
            $comment = null;
            if (preg_match("/COMMENT\\s+'((?:[^'\\\\]|\\\\.)*)'/i", $rest, $cm)) {
                $comment = stripslashes($cm[1]);
            }
            $columns[] = [
                'name'        => $colName,
                'type'        => $colType,
                'length'      => $length,
                'enum_values' => $enumValues,
                'is_null'     => $isNull,
                'default'     => $default,
                'comment'     => $comment,
            ];
        }
        return $columns;
    }

    /**
     * 将 SQL 文件中的表名（可能带旧前缀）解析为实际表名
     * @param string $tableName SQL 文件中的原始表名
     * @param string $prefix    数据库配置前缀
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function resolveTableName(string $tableName, string $prefix): string
    {
        // 替换常见的旧前缀占位符
        $oldPrefixes = ['__PREFIX__', 'xb_'];
        foreach ($oldPrefixes as $old) {
            if (str_starts_with($tableName, $old)) {
                return $prefix . substr($tableName, strlen($old));
            }
        }
        return $tableName;
    }
}
