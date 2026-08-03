<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\app\install\utils;

use Exception;
use think\facade\Db;
use plugin\xbCode\api\Mysql;
use plugin\xbCode\api\Packages;
use plugin\xbCode\api\ConfigApi;
use plugin\xbCode\api\PluginsApi;
use plugin\xbCode\utils\FrameUtil;
use plugin\xbCode\trait\JsonTrait;
use plugin\xbCode\utils\PasswdUtil;

/**
 * 环境检测规则
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class InstallUtil
{
    use JsonTrait;

    /**
     * 安装表结构
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function structure()
    {
        $database = request()->post('database');
        if (empty($database)) {
            throw new Exception('获取安装数据库配置失败');
        }
        // 安装索引
        $index = request()->get('total', 0);
        // 获取全部插件
        $data = PluginsApi::$systemPlugins;
        // 检测全部数据表是否安装完成
        if ($index >= count($data)) {
            return $this->successRes([
                'next' => 'database',
            ], '全部插件安装完成...');
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
            throw new Exception('获取插件失败');
        }
        // 获取插件安装类
        $class = "\\plugin\\{$plugin['name']}\\api\\Install";
        if (!class_exists($class)) {
            throw new Exception("{$plugin['title']}插件安装类不存在");
        }
        if (!method_exists($class, 'install')) {
            throw new Exception("{$plugin['title']}插件安装方法不存在");
        }
        // 获取插件配置
        $config = Packages::config($plugin['name']);
        // 连接数据库
        Mysql::connect($database);
        // 实例安装类
        $class = new $class;
        call_user_func([$class, 'install'], $config['version']);
        // 返回成功
        return $this->successRes([
            'next' => 'structure',
            'total' => $index + 1
        ], "安装 【{$plugin['title']}】 插件成功...");
    }

    /**
     * 安装数据
     * @throws Exception
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function database()
    {
        $site = request()->post('site');
        $database = request()->post('database');
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
            'web_title' => '基于渐进式动态Web开发框架',
            'web_url' => $site['web_url'],
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
        // 返回成功
        return $this->successRes([
            'next' => 'config',
        ], '安装数据完成...');
    }

    /**
     * 安装配置文件
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function config()
    {
        // 获取参数
        $database = request()->post('database');
        $redis = request()->post('redis');
        // 读取模板文件
        $envTplPath = base_path('/plugin/xbCode/app/install/data/env.tpl');
        $envPath = base_path('/.env');
        // 读取配置文件
        $envConfig = file_get_contents($envTplPath);
        // 缓存配置
        $cacheType = $redis['enabled'] ?? '10';
        $cacheType = $cacheType == '20' ? 'redis' : 'file';
        // 替换配置文件参数
        $str1 = [
            // 数据库配置
            "{TYPE}",
            "{HOSTNAME}",
            "{DATABASE}",
            "{USERNAME}",
            "{PASSWORD}",
            "{HOSTPORT}",
            "{PREFIX}",
            // 缓存配置
            "{CACHE_TYPE}",
            // Redis配置
            "{REDIS_HOST}",
            "{REDIS_PORT}",
            "{REDIS_PASSWD}",
            "{REDIS_PREFIX}",
        ];
        $str2 = [
            // 数据库配置
            $database['type'],
            $database['host'],
            $database['database'],
            $database['username'],
            $database['password'],
            $database['port'],
            $database['prefix'],
            // 缓存配置
            $cacheType,
            // Redis配置
            $redis['host'] ?? '127.0.0.1',
            $redis['port'] ?? '6379',
            $redis['password'] ?? '',
            $redis['prefix'] ?? 'xb_',
        ];
        $envConfig = str_replace($str1, $str2, $envConfig);
        // 写入配置文件
        file_put_contents($envPath, $envConfig);
        // 安装系统插件
        foreach (PluginsApi::$systemPlugins as $name => $title) {
            $plugin = PluginsApi::make()->get($name);
            // 安装系统插件记录
            PluginsApi::make()->install($name, $plugin, '20');
            // 启用插件状态
            PluginsApi::make()->state($name, '20');
        }
        // 刷新插件缓存
        PluginsApi::make()->getCache(true);
        // 刷新配置缓存
        ConfigApi::make('')->getCache(true);
        // 重新平滑启动框架
        FrameUtil::delayReload(2);
        // 返回成功
        return $this->successRes([], '应用安装成功，即将跳转...');
    }
}