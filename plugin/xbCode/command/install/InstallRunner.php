<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\command\install;

use Exception;
use think\facade\Db;
use plugin\xbCode\api\Mysql;
use plugin\xbCode\api\Packages;
use plugin\xbCode\api\ConfigApi;
use plugin\xbCode\api\PluginsApi;
use plugin\xbCode\utils\FrameUtil;
use plugin\xbCode\utils\PasswdUtil;
use Webman\ThinkCache\driver\Redis;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 系统安装执行器（命令行）
 *
 * 安装业务流程：
 *   1. 环境检测（由命令类输出）
 *   2. 安装信息校验（数据库 / Redis / 站点）
 *   3. 执行安装（插件表结构、站点数据）
 *   4. 写入安装配置（.env、服务端口、插件记录、缓存）
 *   5. 输出安装信息
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class InstallRunner
{
    /**
     * 数据库配置
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $database;

    /**
     * Redis配置
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $redis;

    /**
     * 站点配置
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $site;

    /**
     * 命令行输出
     * @var OutputInterface
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected OutputInterface $output;

    /**
     * 分配的服务端口（内网访问端口）
     * @var int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected int $serverPort = 0;

    /**
     * 构造函数
     * @param array $database 数据库配置
     * @param array $redis Redis配置
     * @param array $site 站点配置
     * @param OutputInterface $output 命令行输出
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function __construct(array $database, array $redis, array $site, OutputInterface $output)
    {
        $this->database = $database;
        $this->redis = $redis;
        $this->site = $site;
        $this->output = $output;
    }

    /**
     * 数据库配置校验（对应安装控制器 checked 的数据库部分）
     * @param array $database
     * @throws Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function verifyDatabase(array $database): void
    {
        if (empty($database['host'])) {
            throw new Exception('请输入主机地址');
        }
        if (empty($database['database'])) {
            throw new Exception('请输入数据库名称');
        }
        if (empty($database['username'])) {
            throw new Exception('请输入数据库用户');
        }
        if (!isset($database['password']) || $database['password'] === '') {
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
            // 数据库连接失败错误码
            $errorCode = [1040, 1041, 1042, 1043, 1044, 1045];
            if (in_array($e->getCode(), $errorCode)) {
                throw new Exception('数据库连接失败，请检查数据库配置');
            }
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Redis配置校验（对应安装控制器 checked 的 Redis 部分）
     * @param array $redis
     * @throws Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function verifyRedis(array $redis): void
    {
        if (empty($redis)) {
            throw new Exception('Redis配置参数错误');
        }
        // 跳过Redis配置
        $state = $redis['enabled'] ?? '20';
        if ($state === '10') {
            return;
        }
        if (!isset($redis['host']) || empty($redis['host'])) {
            throw new Exception('请输入Redis主机地址');
        }
        if (!isset($redis['port']) || empty($redis['port'])) {
            throw new Exception('请输入Redis主机端口');
        }
        if (!isset($redis['prefix']) || empty($redis['prefix'])) {
            throw new Exception('请输入Redis前缀');
        }
        if (!class_exists('Redis')) {
            throw new Exception('Redis扩展未安装');
        }
        try {
            new Redis([
                'host' => $redis['host'] ?? '127.0.0.1',
                'port' => $redis['port'] ?? 6379,
                'password' => $redis['password'] ?? '',
                'select' => 0,
                'timeout' => 0,
                'expire' => 0,
                'persistent' => false,
                'prefix' => $redis['prefix'] ?? 'xb_',
                'tag_prefix' => 'tag:',
                'serialize' => [],
            ]);
        } catch (\Throwable $th) {
            if (strpos($th->getMessage(), 'password')) {
                throw new Exception('Redis密码错误');
            }
            throw new Exception("Redis错误：{$th->getMessage()}");
        }
    }

    /**
     * 站点配置校验（对应安装控制器 site）
     * @param array $site
     * @throws Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function verifySite(array $site): void
    {
        if (empty($site)) {
            throw new Exception('站点数据错误');
        }
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
    }

    /**
     * 安装前预检（对应安装控制器 checked 的插件安装前置检测）
     * @throws Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function precheck(): void
    {
        foreach (PluginsApi::$systemPlugins as $name => $title) {
            $class = "\\plugin\\{$name}\\api\\Install";
            if (!class_exists($class)) {
                throw new Exception("{$name}插件安装类不存在");
            }
            if (!method_exists($class, 'install')) {
                throw new Exception("{$name}插件安装方法不存在");
            }
            if (!method_exists($class, 'installBefore')) {
                throw new Exception("{$name}插件安装前置方法不存在");
            }
            // 检测安装前依赖（composer 依赖、插件依赖）
            $install = new $class;
            $install->installBefore('');
        }
    }

    /**
     * 执行安装：安装所有系统插件表结构
     * @throws Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function installStructure(): void
    {
        // 连接数据库
        Mysql::connect($this->getDbConfig());
        $title = '';
        foreach (PluginsApi::$systemPlugins as $name => $pluginTitle) {
            $title = $pluginTitle;
            $class = "\\plugin\\{$name}\\api\\Install";
            if (!class_exists($class)) {
                throw new Exception("{$pluginTitle}插件安装类不存在");
            }
            if (!method_exists($class, 'install')) {
                throw new Exception("{$pluginTitle}插件安装方法不存在");
            }
            // 获取插件配置
            $config = Packages::config($name);
            // 实例化安装类并执行安装
            $install = new $class;
            $install->install($config['version']);
            $this->output->writeln("安装 【{$pluginTitle}】 插件成功...");
        }
    }

    /**
     * 执行安装：写入站点配置与管理员数据
     * @throws Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function installData(): void
    {
        // 连接数据库
        Mysql::connect($this->getDbConfig());
        // 当前日期时间
        $dateTime = date('Y-m-d H:i:s');
        // 写入站点配置
        $settings = [
            'web_name' => $this->site['web_name'],
            'web_title' => '基于渐进式动态Web开发框架',
            'web_url' => $this->site['web_url'],
            'web_keywords' => '积木云,xbcode,基于渐进式动态Web开发框架',
            'web_desc' => 'xbcode基于渐进式动态Web开发框架',
            'web_logo' => '',
            'login_bg' => '',
            'login_ad' => '',
            'captcha_state' => '10',
        ];
        ConfigApi::make('system')->set([
            'system' => $settings,
        ]);
        $this->output->writeln('写入站点配置完成...');
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
            'username' => $this->site['username'],
            'password' => PasswdUtil::create($this->site['password']),
            'state' => '20',
            'is_system' => '20',
            'nickname' => '系统管理员',
            'avatar' => '',
        ]);
        $this->output->writeln('写入管理员数据完成...');
    }

    /**
     * 写入安装配置（.env、服务端口、插件记录）
     * @throws Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function writeConfig(): void
    {
        // 连接数据库
        Mysql::connect($this->getDbConfig());
        $database = $this->database;
        $redis = $this->redis;
        // 读取模板文件
        $envTplPath = base_path('/plugin/xbCode/command/install/data/env.tpl');
        $envPath = base_path('/.env');
        if (!file_exists($envTplPath)) {
            throw new Exception('安装配置文件模板不存在');
        }
        // 读取配置文件
        $envConfig = file_get_contents($envTplPath);
        // 缓存配置
        $cacheType = $redis['enabled'] ?? '10';
        $cacheType = $cacheType == '20' ? 'redis' : 'file';
        // 替换配置文件参数
        $str1 = [
            '{HOSTNAME}',
            '{DATABASE}',
            '{USERNAME}',
            '{PASSWORD}',
            '{HOSTPORT}',
            '{PREFIX}',
            '{CACHE_TYPE}',
            '{REDIS_HOST}',
            '{REDIS_PORT}',
            '{REDIS_PASSWD}',
            '{REDIS_PREFIX}',
        ];
        $str2 = [
            $database['host'],
            $database['database'],
            $database['username'],
            $database['password'],
            $database['port'],
            $database['prefix'],
            $cacheType,
            $redis['host'] ?? '127.0.0.1',
            $redis['port'] ?? '6379',
            $redis['password'] ?? '',
            $redis['prefix'] ?? 'xb_',
        ];
        $envConfig = str_replace($str1, $str2, $envConfig);
        // 写入配置文件
        file_put_contents($envPath, $envConfig);
        $this->output->writeln('写入 .env 配置文件完成...');
        // 分配服务端口：以插件模板伪静态文件的端口为基准累加100
        $templateNginx = base_path('/plugin/xbCode/nginx.conf');
        $port = (int) (FrameUtil::xbGetNginxConf($templateNginx) ?: 39000) + 100;
        // 端口被占用时继续累加100，直到找到可用端口
        $attempts = 0;
        while (
            function_exists('socket_create')
            && $attempts < 100
            && !FrameUtil::checkedPort($port)
        ) {
            $port += 100;
            $attempts++;
        }
        // 写回根目录伪静态文件
        FrameUtil::updateServerPort($port);
        // 记录内网访问端口
        $this->serverPort = $port;
        $this->output->writeln("分配服务端口完成：{$port}");
        // 安装系统插件记录
        foreach (PluginsApi::$systemPlugins as $name => $title) {
            $plugin = PluginsApi::make()->get($name);
            // 安装系统插件记录
            PluginsApi::make()->install($name, $plugin, '20');
            // 启用插件状态
            PluginsApi::make()->state($name, '20');
        }
        $this->output->writeln('安装系统插件记录完成...');
        // 刷新插件缓存
        PluginsApi::make()->getCache(true);
        // 刷新配置缓存
        ConfigApi::make('')->getCache(true);
        // 重新平滑启动框架
        FrameUtil::delayReload(2);
    }

    /**
     * 获取安装完成信息（对应安装控制器 complete）
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getCompleteInfo(): array
    {
        // 获取后台地址
        $adminUrl = FrameUtil::getBackendUrl();
        if (!str_starts_with($adminUrl, '/')) {
            $adminUrl = "/{$adminUrl}";
        }
        if (!str_ends_with($adminUrl, '/')) {
            $adminUrl = "{$adminUrl}/";
        }
        // 提示消息词
        $message = '应用安装成功，以下是安装相关信息';
        // 返回成功
        return [
            'message' => $message,
            'admin_url' => $adminUrl,
        ];
    }

    /**
     * 获取分配的服务端口（内网访问端口）
     * @return int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getServerPort(): int
    {
        return $this->serverPort;
    }

    /**
     * 获取数据库连接配置（使用 think-orm 原生键名）
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getDbConfig(): array
    {
        return [
            'type' => $this->database['type'] ?? 'mysql',
            'hostname' => $this->database['host'],
            'hostport' => (string) $this->database['port'],
            'database' => $this->database['database'],
            'username' => $this->database['username'],
            'password' => $this->database['password'],
            'charset' => 'utf8mb4',
            'prefix' => $this->database['prefix'],
        ];
    }
}
