<?php
namespace app\install\utils;

use Exception;
use support\Cache;
use support\Request;
use think\facade\Db;
use xbcode\trait\JsonTrait;
use xbcode\utils\MysqlUtil;
use xbcode\utils\PasswdUtil;

/**
 * 安装工具类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class InstallUtil
{
    // 使用Json工具
    use JsonTrait;

    /**
     * 验证并缓存安装数据
     * @param array $post
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function dataChecked(array $post)
    {
        $database = $post['database'];
        $redis    = $post['redis'];
        if (empty($database) || empty($redis)) {
            throw new Exception('参数错误');
        }
        if (empty($database['host'])) {
            throw new Exception('请输入主机地址');
        }
        if (empty($database['database'])) {
            throw new Exception('请输入数据库名称');
        }
        if (empty($database['username'])) {
            throw new Exception('请输入数据库用户');
        }
        if (empty($database['password'])) {
            throw new Exception('请输入数据库密码');
        }
        if (empty($database['port'])) {
            throw new Exception('请输入数据库端口');
        }
        try {
            $dsn = "mysql:host={$database['host']};dbname={$database['database']};port={$database['port']};";
            $params = [
                \PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8mb4",
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::ATTR_TIMEOUT => 5,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ];
            new \PDO($dsn, $database['username'], $database['password'], $params);
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        // 验证redis
        if (!isset($redis['host']) || empty($redis['host'])) {
            throw new Exception('请输入Redis主机地址');
        }
        if (!isset($redis['port']) || empty($redis['port'])) {
            throw new Exception('请输入Redis主机端口');
        }
        if (!isset($redis['prefix']) || empty($redis['prefix'])) {
            throw new Exception('请输入Redis前缀');
        }
        // 缓存数据
        Cache::set('install_database', $database, 3600);
        Cache::set('install_redis', $redis, 3600);
    }

    /**
     * 验证并缓存站点数据
     * @param array $post
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function siteChecked(array $post)
    {
        // 检测数据库配置缓存是否生效
        if (!Cache::get('install_database') || !Cache::get('install_redis')) {
            throw new Exception('请重新填写数据库配置');
        }
        $site = $post['site'];
        // 验证站点数据
        if (!isset($site['web_name']) || empty($site['web_name'])) {
            throw new Exception('请输入站点名称');
        }
        if (!isset($site['web_url']) || empty($site['web_url'])) {
            throw new Exception('请输入站点域名');
        }
        if (!filter_var($site['web_url'], FILTER_SANITIZE_URL)) {
            throw new Exception('请输入正确的域名地址');
        }
        if (!isset($site['username']) || empty($site['username'])) {
            throw new Exception('请输入站点管理员账号');
        }
        if (strlen($site['username']) < 5) {
            throw new Exception('管理员账号长度不能小于5位');
        }
        if (!isset($site['password']) || empty($site['password'])) {
            throw new Exception('请输入站点管理员密码');
        }
        if (strlen($site['password']) < 6 || strlen($site['password']) > 20) {
            throw new Exception('管理员密码不能小于6位或大于20位');
        }
        // 缓存数据
        Cache::set('install_site', $site, 3600);
    }

    /**
     * 安装表结构
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function structure(Request $request)
    {
        // 数据库连接
        $total    = $request->get('total', 0);
        $database = Cache::get('install_database');
        if (empty($database)) {
            return self::fail('获取安装数据库配置失败');
        }
        // SQL目录地址
        $sqlPath = app_path() . "/install/data/sql/*.sql";
        // 获取SQL文件树
        $sqlFiles = glob($sqlPath);
        // 检测文件列表
        if (empty($sqlFiles)) {
            return self::fail('获取SQL文件失败');
        }
        // 检测全部数据表是否安装完成
        if ($total >= count($sqlFiles)) {
            return self::successFul("安装数据库结构完成...", [
                'next' => 'database',
            ]);
        }
        // 获取SQL文件
        $sqlFile = isset($sqlFiles[$total]) ? $sqlFiles[$total] : '';
        if (!$sqlFile) {
            return self::fail('获取SQL文件失败');
        }
        // 连接数据库
        MysqlUtil::connect($database);
        // 导入sql
        MysqlUtil::importSql($sqlFile, '`xb_', "`{$database['prefix']}");
        // 获取数据表名称
        $prefixs     = ['php_', 'xb_', '__PREFIX__'];
        $installName = str_replace($prefixs, '', basename($sqlFile, '.sql'));
        // 返回成功
        return self::successFul("【{$installName}】 安装数据表成功...", [
            'next' => 'structure',
            'total' => $total + 1
        ]);
    }
    
    /**
     * 安装数据
     * @param \support\Request $request
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function database(Request $request)
    {
        $site     = Cache::get('install_site');
        $database = Cache::get('install_database');
        if (empty($site) || empty($database)) {
            throw new Exception('获取安装站点配置失败');
        }
        // 当前日期时间
        $dateTime = date('Y-m-d H:i:s');
        // 连接数据库
        MysqlUtil::connect($database);
        // 写入站点配置
        $settings = [
            'web_name' => $site['web_name'],
            'web_url' => $site['web_url'],
            'web_title' => '',
            'web_keywords' => '',
            'web_description' => '',
            'web_logo' => ''
        ];
        foreach ($settings as $field => $value) {
            Db::name('config')->save([
                'create_at' => $dateTime,
                'update_at' => $dateTime,
                'group' => 'system',
                'name' => $field,
                'value' => $value
            ]);
        }
        // 上传配置
        Db::name('config')->save([
            'create_at' => $dateTime,
            'update_at' => $dateTime,
            'group' => 'upload',
            'name' => 'active',
            'value' => 'local',
        ]);
        Db::name('config')->save([
            'create_at' => $dateTime,
            'update_at' => $dateTime,
            'group' => 'upload',
            'name' => 'local',
            'value' => json_encode([
                'type' => 'local',
                'state' => '20'
            ]),
        ]);

        // 写入总后台权限角色
        $roleId = Db::name('admin_role')->insertGetId([
            'create_at' => $dateTime,
            'update_at' => $dateTime,
            'admin_id' => 0,
            'title' => '系统管理员',
            'is_system' => '20'
        ]);
        // 写入总后台管理员
        Db::name('admin')->save([
            'create_at' => $dateTime,
            'update_at' => $dateTime,
            'role_id' => $roleId,
            'admin_id' => 0,
            'username' => $site['username'],
            'password' => PasswdUtil::create($site['password']),
            'state' => '20',
            'is_system' => '20',
            'nickname' => '系统管理员',
            'avatar' => ''
        ]);
    }
    
    /**
     * 写入配置文件
     * @param \support\Request $request
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function config(Request $request)
    {
        // 获取参数
        $redis    = Cache::get('install_redis');
        $database = Cache::get('install_database');
        // 读取模板文件
        $envTplPath = app_path() . '/install/data/env.tpl';
        $envPath    = base_path() . '/.env';
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
    }

    /**
     * 判断是否已经安装
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function hasInstall()
    {
        $path = base_path() . '/.env';
        if (file_exists($path)) {
            return true;
        }
        return false;
    }
}