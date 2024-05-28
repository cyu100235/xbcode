<?php
namespace app\common\providers;

use think\facade\Db;
use Exception;

/**
 * 最方便的mysql操作类,可以便捷导入.sql文件和将数据库导出为.sql文件
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class MysqlProvider
{
    /**
     * 获取数据库所有表名
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getTableNames()
    {
        $result = Db::query('SELECT TABLE_NAME as table_name FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE()');
        return array_column($result, 'table_name');
    }

    /**
     * 获取数据表结构
     * @param string $name
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getTable(string $name)
    {
        // 获取表结构
        $result = Db::query("SELECT * FROM INFORMATION_SCHEMA.`TABLES` WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$name}';");
        if (!$result) {
            throw new Exception("数据表不存在：{$name}");
        }
        return current($result);
    }

    /**
     * 获取数据表字段
     * @param string $name
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getColumns(string $name)
    {
        $result = Db::query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$name}';");
        $result = array_map(function ($item) {
            // 获取字段长度
            $length = $item['DATA_TYPE'] === 'int' ? $item['NUMERIC_PRECISION'] : $item['CHARACTER_MAXIMUM_LENGTH'];
            return [
                'name' => $item['COLUMN_NAME'],
                'type' => $item['DATA_TYPE'],
                'length' => $length,
                'comment' => $item['COLUMN_COMMENT'],
                'default' => $item['COLUMN_DEFAULT'],
                'is_null' => $item['IS_NULLABLE'] === 'YES',
                'is_auto' => $item['EXTRA'] === 'auto_increment',
            ];
        }, $result);
        return $result;
    }

    /**
     * 获取数据表-列表
     * @param array $ignore
     * @return array[]
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getTableList(array $ignore = [])
    {
        $tableNames = self::getTableNames();
        $data       = [];
        foreach ($tableNames as $key => $name) {
            if (in_array($name, $ignore)) {
                continue;
            }
            $table      = self::getTable($name);
            $data[$key] = [
                'create_at' => $table['CREATE_TIME'],
                'name' => $name,
                'title' => $table['TABLE_COMMENT'],
                'rows' => self::getTablesRows($name),
                'engine' => $table['ENGINE'],
                'charset' => $table['TABLE_COLLATION'],
            ];
        }
        return $data;
    }

    /**
     * 获取表的记录数
     * @param string $tableName
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getTablesRows(string $tableName)
    {
        return Db::table($tableName)->count();
    }

    /**
     * 将.sql文件导入到mysql数据库
     * @param string $sqlFilePath SQL文件路径
     * @param string|array $oldPrefix 您的sql文件表前缀，空则使用__PREFIX__
     * @param string $prefix 最终创建表前缀，空则使用配置文件的前缀
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function importSql(string $sqlFilePath, string|array $oldPrefix = '__PREFIX__', string $prefix = '')
    {
        if (!file_exists($sqlFilePath)) {
            throw new Exception('sql文件不存在');
        }
        // 获取数据库表前缀
        $prefix = $prefix ?: config('thinkorm.connections.mysql.prefix', '');

        //读取.sql文件内容
        $sqlContent = file($sqlFilePath);

        $tmp = '';
        // 执行每个SQL语句
        foreach ($sqlContent as $line) {
            // 跳过空行和注释
            if (trim($line) == '' || stripos(trim($line), '--') === 0 || stripos(trim($line), '/*') === 0) {
                continue;
            }
            // 拼接SQL语句
            $tmp .= $line;
            // 如果语句以分号结尾，执行SQL语句
            if (substr(trim($line), -1) === ';') {
                $tmp = str_ireplace($oldPrefix, $prefix, $tmp);
                $tmp = str_ireplace('INSERT INTO ', 'INSERT IGNORE INTO ', $tmp);
                Db::execute($tmp);
                $tmp = '';
            }
        }
    }

    /**
     * 将mysql数据库表结构和数据导出为.sql文件
     * @param string $sqlFilePath 导出的.sql文件路径
     * @param bool $withData 是否导出表数据(默认为true)
     * @param array $tables 要导出的表名数组(默认为空，即导出所有表)
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function exportSql(string $sqlFilePath, bool $withData = true, array $tables = [])
    {
        // 获取所有表名显示列名为table_name
        $result = Db::query('SELECT TABLE_NAME as table_name FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE()');
        // 获取所有表名
        $allTables = array_column($result, 'table_name');
        // 打开输出文件
        $outputFile = fopen($sqlFilePath, 'w');

        // 循环每个表，导出结构和数据
        foreach ($allTables as $tableName) {
            // 如果指定了要导出的表，检查是否在其中
            if (!empty($tables) && !in_array($tableName, $tables)) {
                continue;
            }
            // 导出表结构
            fwrite($outputFile, "-- 表结构：$tableName\n");
            // 获取表结构
            $showTableInfo = Db::query("SHOW CREATE TABLE {$tableName}");
            $sqlInfo       = $showTableInfo[0]['Create Table'] ?? '';
            fwrite($outputFile, $sqlInfo . ";\n");

            if ($withData) {
                // 导出表数据
                $result = Db::query("SELECT * FROM {$tableName}");
                if (!$result) {
                    fwrite($outputFile, "/* " . $tableName . "表没有数据 */\n");
                } else {
                    fwrite($outputFile, "-- 表数据：$tableName\n");
                    foreach ($result as $row) {
                        // 输出表数据
                        $escapedValues = array_map(function ($value) {
                            return addslashes($value);
                        }, $row);
                        // 转义数据
                        $columns = implode("','", $escapedValues);
                        fwrite($outputFile, "INSERT INTO `$tableName` VALUES ('$columns');\n");
                    }
                }
            }
        }
    }
}