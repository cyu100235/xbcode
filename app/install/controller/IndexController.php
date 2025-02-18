<?php
namespace plugin\xbCode\app\install\controller;

use Exception;
use support\Cache;
use support\Request;
use think\facade\Db;
use plugin\xbCode\api\Mysql;
use plugin\xbCode\api\Install;
use plugin\xbCode\api\Packages;
use plugin\xbCode\XbController;
use plugin\xbCode\utils\FrameUtil;
use plugin\xbCode\utils\PasswdUtil;
use plugin\xbCode\app\install\utils\EnvironmentUtil;

/**
 * 安装控制器
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class IndexController extends XbController
{
    /**
     * 构造方法
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function init()
    {
        // 请求地址
        $path = trim(request()->path(), '/');
        $ignore = [
            'install',
            'app/xbCode/install/Index/protocol',
            'app/xbCode/install/Index/complete',
        ];
        // 检测是否安装
        if (Install::checked() && !in_array($path, $ignore)) {
            throw new Exception('已经安装，无需重复安装', 10000);
        }
    }
    
    /**
     * 安装视图
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        return $this->view('public/install/index', 'html');
    }

    /**
     * 安装协议
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function protocol()
    {
        $path = dirname(__DIR__) . '/data/agreement.txt';
        if (!file_exists($path)) {
            return $this->successRes([
                'content' => '',
            ]);
        }
        $content = file_get_contents($path);
        $data = [
            'content' => $content,
        ];
        return $this->successRes($data);
    }

    /**
     * 环境检测
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function environment()
    {
        $data = EnvironmentUtil::get();
        return $this->successRes($data);
    }

    /**
     * 数据库配置检测
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function checked(Request $request)
    {
        $post = request()->post();
        if (empty($post)) {
            return $this->fail('参数错误');
        }
        // 执行插件验证
        $data = Packages::getPackages($request->plugin, 'plugins');
        foreach ($data as $name => $value) {
            $class = "\\plugin\\{$name}\\api\\Install";
            if (!class_exists($class)) {
                return $this->fail("{$name}插件安装类不存在");
            }
            if (!method_exists($class, 'install')) {
                return $this->fail("{$name}插件安装方法不存在");
            }
            // 获取插件配置
            $configPath = base_path() . "/plugin/{$name}/config/app.php";
            if (empty($configPath)) {
                return $this->fail("{$value}插件配置文件不存在");
            }
            if (!method_exists($class, 'installBefore')) {
                return $this->fail("{$name}插件安装前置方法不存在");
            }
            // 获取插件配置
            $config = include $configPath;
            if (empty($config['version'])) {
                return $this->fail("{$name}插件版本号不存在");
            }
            // 检测安装前检测
            $class::installBefore($config['version']);
        }
        try {
            $database = $post['database'];
            $redis = $post['redis'];
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
        } catch (\Throwable $th) {
            // 检测是否数据库连接失败
            $errorCode = [
                1040,
                1041,
                1042,
                1043,
                1044,
                1045,
            ];
            if (in_array($th->getCode(), $errorCode)) {
                return $this->fail('数据库连接失败，请检查数据库配置');
            }
            // 返回错误
            return $this->fail($th->getMessage());
        }
        // 返回成功
        return $this->success('数据验证成功');
    }

    /**
     * 站点设置检测
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function site()
    {
        $post = request()->post();
        if (empty($post)) {
            return $this->fail('参数错误');
        }
        // 检测数据库配置缓存是否生效
        if (!Cache::get('install_database') || !Cache::get('install_redis')) {
            return $this->fail('请重新填写数据库配置');
        }
        $site = $post['site'];
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
        Cache::set('install_site', $site, 3600);
        // 返回成功
        return $this->success('数据验证成功');
    }

    /**
     * 安装数据
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function install(Request $request)
    {
        // 获取安装步骤
        $step = $request->get('step', '');
        // 执行插件安装
        if ($step === 'structure') {
            $database = Cache::get('install_database');
            if (empty($database)) {
                return self::fail('获取安装数据库配置失败');
            }
            // 安装索引
            $index = $request->get('total', 0);
            // 获取全部插件
            $data = Packages::getPackages($request->plugin, 'plugins');
            // 追加主应用插件
            $data = array_merge(['xbCode' => '小白基础应用'], $data);
            // 检测全部数据表是否安装完成
            if ($index >= count($data)) {
                return self::successFul("全部插件安装完成...", [
                    'next' => 'database',
                ]);
            }
            $plugins = [];
            foreach ($data as $name => $value) {
                $plugins[] = [
                    'name' => $name,
                    'title' => $value,
                ];
            }
            // 获取安装的插件
            $plugin = $plugins[$index] ?? '';
            if (empty($plugin)) {
                return self::fail('获取插件失败');
            }
            // 获取插件安装类
            $class = "\\plugin\\{$plugin['name']}\\api\\Install";
            if (!class_exists($class)) {
                return self::fail("{$plugin['title']}插件安装类不存在");
            }
            if (!method_exists($class, 'install')) {
                return self::fail("{$plugin['title']}插件安装方法不存在");
            }
            $configPath = base_path() . "/plugin/{$plugin['name']}/config/app.php";
            if (empty($configPath)) {
                return self::fail("{$plugin['title']}插件配置文件不存在");
            }
            $config = include $configPath;
            if (empty($config['version'])) {
                return self::fail("{$plugin['title']}插件版本号不存在");
            }
            // 连接数据库
            Mysql::connect($database);
            // 执行安装
            $class::install($config['version']);
            // 返回成功
            return self::successFul("安装 【{$plugin['title']}】 插件成功...", [
                'next' => 'structure',
                'total' => $index + 1
            ]);
        } else if ($step === 'database') {
            // 安装数据
            $this->appDatabase($request);
            // 返回成功
            return $this->successFul('安装数据完成...', [
                'next' => 'config',
            ]);
        } else if ($step === 'config') {
            // 安装配置文件
            $this->appConfig($request);
            // 重启框架
            FrameUtil::delayReload(2);
            // 返回成功
            return $this->success('应用安装成功，即将跳转...');
        } else {
            // 安装失败
            return $this->fail('安装失败...');
        }
    }

    /**
     * 安装完成
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function complete()
    {
        // 获取后台地址
        $adminUrl = getenv('ADMIN_URL');
        // 返回成功
        return $this->successFul('应用已经安装',[
            'admin_url' => "/{$adminUrl}/"
        ]);
    }

    /**
     * 安装数据
     * @param \support\Request $request
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function appDatabase(Request $request)
    {
        $site = Cache::get('install_site');
        $database = Cache::get('install_database');
        if (empty($site) || empty($database)) {
            throw new Exception('获取安装站点配置失败');
        }
        // 当前日期时间
        $dateTime = date('Y-m-d H:i:s');
        // 连接数据库
        Mysql::connect($database);
        // 写入站点配置
        $settings = [
            'web_name' => $site['web_name'],
            'web_url' => $site['web_url'],
            'web_title' => '',
            'web_keywords' => '',
            'web_description' => '',
            'web_logo' => '',
        ];
        foreach ($settings as $field => $value) {
            Db::name('config')->save([
                'create_at' => $dateTime,
                'update_at' => $dateTime,
                'group' => 'system',
                'name' => $field,
                'value' => $value,
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
                'state' => '20',
            ]),
        ]);

        // 写入总后台权限角色
        $roleId = Db::name('admin_role')->insertGetId([
            'create_at' => $dateTime,
            'update_at' => $dateTime,
            'admin_id' => 0,
            'title' => '系统管理员',
            'is_system' => '20',
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
            'avatar' => '',
        ]);
    }

    /**
     * 写入配置文件
     * @param \support\Request $request
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function appConfig(Request $request)
    {
        // 获取参数
        $redis = Cache::get('install_redis');
        $database = Cache::get('install_database');
        // 读取模板文件
        $envTplPath = base_path() . '/plugin/xbCode/app/install/data/env.tpl';
        $envPath = base_path() . '/.env';
        // 读取配置文件
        $envConfig = file_get_contents($envTplPath);
        // 获取宝塔环境配置
        $btEnvState = 'false';
        $btEnvName = $request->host();
        // 替换配置文件参数
        $str1 = [
            // 宝塔环境配置
            "{BT_ENV_STATE}",
            "{BT_ENV_NAME}",
            // 数据库配置
            "{TYPE}",
            "{HOSTNAME}",
            "{DATABASE}",
            "{USERNAME}",
            "{PASSWORD}",
            "{HOSTPORT}",
            "{PREFIX}",
            // Redis配置
            "{REDIS_HOST}",
            "{REDIS_PORT}",
            "{REDIS_PASSWD}",
            "{REDIS_PREFIX}",
        ];
        $str2 = [
            // 宝塔环境配置
            $btEnvState,
            $btEnvName,
            // 数据库配置
            $database['type'],
            $database['host'],
            $database['database'],
            $database['username'],
            $database['password'],
            $database['port'],
            $database['prefix'],
            // Redis配置
            $redis['host'],
            $redis['port'],
            $redis['password'],
            $redis['prefix'],
        ];
        $envConfig = str_replace($str1, $str2, $envConfig);
        // 写入配置文件
        file_put_contents($envPath, $envConfig);
    }
}
