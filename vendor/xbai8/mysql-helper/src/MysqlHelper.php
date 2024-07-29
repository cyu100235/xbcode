<?php
declare (strict_types=1);

namespace xbai8;

use InvalidArgumentException;
use mysqli;

/**
 * 最方便的mysql操作类,可以便捷导入.sql文件和将数据库导出为.sql文件
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class MysqlHelper
{
    /**
     * @var string 服务器地址
     */
    private $host = '127.0.0.1';
    /**
     * @var int 端口号
     */
    private $port = 3306;
    /**
     * @var string 用户名
     */
    private $username;
    /**
     * @var string 密码
     */
    private $password;
    /**
     * @var string 数据库名
     */
    private $database;
    /**
     * @var string 字符集
     */
    private $charset = 'utf8mb4';

    /**
     * @var string 表前缀
     */
    private $prefix = '';

    /**
     * 数据库连接
     * @var mysqli|null
     */
    private $connect;

    /**
     * 构造函数
     * @param string|null $username 用户名
     * @param string|null $password 密码
     * @param string|null $database 数据库名
     * @param string      $host     服务器地址(默认为127.0.0.1)
     * @param string|int  $port     端口号(默认为3306)
     * @param string      $prefix   表前缀(默认为空)
     * @param string      $charset  字符集(默认为utf8mb4)
     */
    public function __construct(string $username = null, string $password = null, string $database = null, string $host = '127.0.0.1', $port = 3306, string $prefix = '', string $charset = 'utf8mb4')
    {
        if (!in_array($charset, ['utf8mb4', 'utf8', 'gbk', 'gb2312'])) {
            throw new InvalidArgumentException('字符集只能是 utf8mb4, utf8, gbk 或 gb2312');
        }
        if (!is_numeric($port)) {
            throw new InvalidArgumentException('端口号必须是数字');
        }

        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
        $this->host = $host;
        $this->port = intval($port);
        $this->prefix = $prefix;
        $this->charset = $charset;

        // 创建MySQL连接
        $this->connect = new mysqli(
            $this->host,
            $this->username,
            $this->password,
            $this->database,
            $this->port
        );

        // 检查连接是否成功
        if ($this->connect->connect_error) {
            throw new \mysqli_sql_exception("数据库连接失败: " . $this->connect->connect_error);
        }
        // 设置编码
        $this->connect->set_charset($this->charset);
    }

    /**
     * 获取数据库连接
     * @return mysqli|null
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function getConnect()
    {
        return $this->connect;
    }

    /**
     * 开启事务
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function startTrans()
    {
        // 设置非自动提交事务
        $this->connect->autocommit(false);
        // 开启事务
        return $this->connect->begin_transaction();
    }

    /**
     * 提交事务
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function commit()
    {
        return $this->connect->commit();
    }

    /**
     * 回滚事务
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function rollback()
    {
        return $this->connect->rollback();
    }
    
    /**
     * 将.sql文件导入到mysql数据库
     * @param string $sqlFilePath SQL文件路径
     * @param string $prefix     表前缀(默认为空)
     * @param string|array $oldPrefix 旧表前缀(默认为__PREFIX__)
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function importSqlFile(string $sqlFilePath, string $prefix = '', string|array $oldPrefix = '__PREFIX__')
    {
        if (!file_exists($sqlFilePath)) {
            throw new InvalidArgumentException('sql文件不存在');
        }

        $prefix = $prefix ?: $this->prefix;

        //读取.sql文件内容
        $sqlContent = file($sqlFilePath);

        $tmp = '';
        // 执行每个SQL语句
        foreach ($sqlContent as $line) {
            if (trim($line) == '' || stripos(trim($line), '--') === 0 || stripos(trim($line), '/*') === 0) {
                continue;
            }

            $tmp .= $line;
            if (substr(trim($line), -1) === ';') {
                $tmp = str_ireplace($oldPrefix, $prefix, $tmp);
                $tmp = str_ireplace('INSERT INTO ', 'INSERT IGNORE INTO ', $tmp);
                $result = $this->connect->query($tmp);
                if (!$result) {
                    throw new \mysqli_sql_exception("导入失败: " . $this->connect->error);
                }
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
    public function exportSqlFile(string $sqlFilePath, bool $withData = true, array $tables = [])
    {
        // 获取所有表名
        $result = $this->connect->query("SHOW TABLES");
        $all_tables = [];

        // 取出表结果集数量
        while ($row = $result->fetch_row()) {
            $all_tables[] = $row[0];
        }

        // 打开输出文件
        $outputFile = fopen($sqlFilePath, 'w');

        // 循环每个表，导出结构和数据
        foreach ($all_tables as $table) {
            if (!empty($tables) && !in_array($table, $tables)) {
                continue;
            }
            //如果设置了表前缀,且传入的表名不包含表前缀,则补上
            if (!empty($this->prefix) && strpos($table, $this->prefix) !== 0) {
                $table = $this->prefix . $table;
            }

            // 导出表结构
            fwrite($outputFile, "-- 表结构：$table\n");
            $createTableSQL = $this->connect->query("SHOW CREATE TABLE $table");
            $createTableRow = $createTableSQL->fetch_row();
            fwrite($outputFile, $createTableRow[1] . ";\n");

            if ($withData) {
                // 导出表数据
                $result = $this->connect->query("SELECT * FROM $table");
                if (!$result) {
                    fwrite($outputFile, "/* 查询失败或" . $table . "表不存在 */\n");
                } else if ($result->num_rows == 0) {
                    fwrite($outputFile, "/* " . $table . "表没有数据 */\n");
                } else {
                    fwrite($outputFile, "-- 表数据：$table\n");
                    while ($row = $result->fetch_assoc()) {
                        $escapedValues = array_map(function ($value) {
                            return $this->connect->escape_string(strval($value));
                        }, $row);
                        $columns = implode("','", $escapedValues);
                        fwrite($outputFile, "INSERT INTO `$table` VALUES ('$columns');\n");
                    }
                    //释放结果集
                    $result->free();
                }
            }
        }
        // 关闭文件和连接
        fclose($outputFile);
    }

    /**
     * 析构函数
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function __destruct()
    {
        $this->connect->close();
    }
}