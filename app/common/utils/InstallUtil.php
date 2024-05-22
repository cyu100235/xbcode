<?php
namespace app\common\utils;

use Exception;
use support\Request;
use think\facade\Cache;
use xbai8\MysqlHelper;

/**
 * 安装工具类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class InstallUtil
{
    use JsonUtil;

    /**
     * 缓存安装数据
     * @param \support\Request $request
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function cache(Request $request)
    {
        $database = $request->post('database');
        $redis    = $request->post('redis');
        $site     = $request->post('site');
        if (empty($database) || empty($site)) {
            return $this->fail('参数错误');
        }

        if (empty($database['host'])) {
            return $this->fail('请输入主机地址');
        }
        if (empty($database['database'])) {
            return $this->fail('请输入数据库名称');
        }
        if (empty($database['username'])) {
            return $this->fail('请输入数据库用户');
        }
        if (empty($database['password'])) {
            return $this->fail('请输入数据库密码');
        }
        if (empty($database['port'])) {
            return $this->fail('请输入数据库端口');
        }
        try {
            $dsn    = "mysql:host={$database['host']};dbname={$database['database']};port={$database['port']};";
            $params = [
                \PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8mb4",
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::ATTR_TIMEOUT => 5,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ];
            new \PDO($dsn, $database['username'], $database['password'], $params);
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }

        // 验证redis
        if (!isset($redis['host']) || empty($redis['host'])) {
            return $this->fail('请输入Redis主机地址');
        }
        if (!isset($redis['port']) || empty($redis['port'])) {
            return $this->fail('请输入Redis主机端口');
        }
        if (!isset($redis['prefix']) || empty($redis['prefix'])) {
            return $this->fail('请输入Redis前缀');
        }

        // 验证站点数据
        if (!isset($site['web_name']) || empty($site['web_name'])) {
            return $this->fail('请输入站点名称');
        }
        if (!isset($site['web_url']) || empty($site['web_url'])) {
            return $this->fail('请输入站点域名');
        }
        if (!filter_var($site['web_url'], FILTER_SANITIZE_URL)) {
            return $this->fail('请输入正确的域名地址');
        }
        if (!isset($site['username']) || empty($site['username'])) {
            return $this->fail('请输入站点管理员账号');
        }
        if (strlen($site['username']) < 5) {
            return $this->fail('管理员账号长度不能小于5位');
        }
        if (!isset($site['password']) || empty($site['password'])) {
            return $this->fail('请输入站点管理员密码');
        }
        if (strlen($site['password']) < 6 || strlen($site['password']) > 20) {
            return $this->fail('管理员密码不能小于6位或大于20位');
        }
        // 缓存数据
        Cache::set('install_database', $database, 30);
        Cache::set('install_redis', $redis, 30);
        Cache::set('install_site', $site, 30);
        // 返回数据
        return $this->success('success');
    }

    /**
     * 安装表结构
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function structure(Request $request)
    {
        // 数据库连接
        $total    = $request->get('total', 0);
        $database = Cache::get('install_database');
        if (empty($database)) {
            return $this->fail('获取安装数据库配置失败');
        }
        // 获取SQL文件树
        $sqlFiles = glob(app_path('data/sql/*.sql'));
        // 检测全部数据表是否安装完成
        if ($total >= count($sqlFiles)) {
            return $this->successFul("安装数据库结构完成...", [
                'next' => 'database',
            ]);
        }
        // 获取SQL文件
        $sqlFile = isset($sqlFiles[$total]) ? $sqlFiles[$total] : '';
        if (!$sqlFile) {
            return $this->fail('获取SQL文件失败');
        }
        // 实例操作类
        $mysql = new MysqlHelper(
            $database['username'],
            $database['password'],
            $database['database'],
            $database['host'],
            $database['port'],
            $database['prefix'],
            $database['charset']
        );
        // 导入SQL文件
        $mysql->importSqlFile($sqlFile, $database['prefix'], 'xb_');
        // 获取数据表名称
        $installName = str_replace(['php_', 'xb_',], '', basename($sqlFile, '.sql'));
        // 返回成功
        return $this->successFul("【{$installName}】 安装数据表成功...", [
            'next' => 'structure',
            'total' => $total + 1
        ]);
    }

    /**
     * 安装表数据
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function database(Request $request)
    {
        $site     = Cache::get('install_site');
        $database = Cache::get('install_database');
        // 实例操作类
        $mysql    = new MysqlHelper(
            $database['username'],
            $database['password'],
            $database['database'],
            $database['host'],
            $database['port'],
            $database['prefix'],
            $database['charset']
        );
        $connect  = $mysql->getConnect();
        $dateTime = date('Y-m-d H:i:s');
        // 写入站点名称
        $connect->query("INSERT INTO `{$database['prefix']}settings` (`create_at`,`update_at`,`name`, `value`,`group`) VALUES ('{$dateTime}','{$dateTime}','web_name', '{$site['web_name']}','system')");
        $connect->query("INSERT INTO `{$database['prefix']}settings` (`create_at`,`update_at`,`name`, `value`,`group`) VALUES ('{$dateTime}','{$dateTime}','web_url', '{$site['web_url']}','system')");
        $connect->query("INSERT INTO `{$database['prefix']}settings` (`create_at`,`update_at`,`name`, `value`,`group`) VALUES ('{$dateTime}','{$dateTime}','web_title', '站点标题','system')");
        $connect->query("INSERT INTO `{$database['prefix']}settings` (`create_at`,`update_at`,`name`, `value`,`group`) VALUES ('{$dateTime}','{$dateTime}','web_keywords', '站点关键词','system')");
        $connect->query("INSERT INTO `{$database['prefix']}settings` (`create_at`,`update_at`,`name`, `value`,`group`) VALUES ('{$dateTime}','{$dateTime}','web_description', '站点描述','system')");
        $connect->query("INSERT INTO `{$database['prefix']}settings` (`create_at`,`update_at`,`name`, `value`,`group`) VALUES ('{$dateTime}','{$dateTime}','web_logo', '','system')");
        $connect->query("INSERT INTO `{$database['prefix']}settings` (`create_at`,`update_at`,`name`, `value`,`group`) VALUES ('{$dateTime}','{$dateTime}','active', 'public','upload')");
        $connect->query("INSERT INTO `{$database['prefix']}settings` (`create_at`,`update_at`,`name`, `value`,`group`) VALUES ('{$dateTime}','{$dateTime}','public.root', '/uploads','upload')");
        $connect->query("INSERT INTO `{$database['prefix']}settings` (`create_at`,`update_at`,`name`, `value`,`group`) VALUES ('{$dateTime}','{$dateTime}','public.url', '{$site['web_url']}','upload')");

        // 写入管理员
        $site['password'] = PasswordUtil::passwordHash($site['password']);
        $connect->query("INSERT INTO `{$database['prefix']}admin` (`create_at`,`update_at`,`role_id`, `username`,`password`,`state`,`nickname`,`is_system`) VALUES ('{$dateTime}','{$dateTime}','1','{$site['username']}','{$site['password']}','20','楚羽幽','20');");

        // 安装数据完成
        return $this->successFul('安装表数据完成...', [
            'next' => 'config'
        ]);
    }

    /**
     * 安装配置文件
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function config(Request $request)
    {
        // 获取参数
        $redis    = Cache::get('install_redis');
        $database = Cache::get('install_database');
        // 读取模板文件
        $envTplPath = app_path('data/env.tpl');
        $envPath    = base_path('/.env');
        // 读取配置文件
        $envConfig = file_get_contents($envTplPath);
        // 替换配置文件参数
        $str1      = [
            "{TYPE}",
            "{HOSTNAME}",
            "{DATABASE}",
            "{USERNAME}",
            "{PASSWORD}",
            "{HOSTPORT}",
            "{PREFIX}",
            "{REDIS_HOST}",
            "{REDIS_PORT}",
            "{REDIS_PASSWD}",
            "{REDIS_PREFIX}"
        ];
        $str2      = [
            $database['type'],
            $database['host'],
            $database['database'],
            $database['username'],
            $database['password'],
            $database['port'],
            $database['prefix'],
            $redis['host'],
            $redis['port'],
            $redis['password'],
            $redis['prefix']
        ];
        $envConfig = str_replace($str1, $str2, $envConfig);
        // 写入配置文件
        file_put_contents($envPath, $envConfig);
        // 延迟1秒重启
        FrameUtil::pcntlAlarm(1, function () {
            // 重启服务
            FrameUtil::reload();
        });
        // 返回成功
        return $this->success('安装配置完成，3秒后跳转...');
    }

    /**
     * 判断是否已经安装
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function hasInstall()
    {
        if (file_exists(base_path('.env'))) {
            return true;
        }
        return false;
    }
}