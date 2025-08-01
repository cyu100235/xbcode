<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @version  1.0
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\app\install\utils;

use Exception;
use plugin\xbCode\utils\PasswdUtil;
use support\Request;
use support\think\Cache;
use plugin\xbCode\api\Mysql;
use plugin\xbCode\api\Packages;
use plugin\xbCode\utils\FrameUtil;
use plugin\xbCode\utils\trait\JsonTrait;
use think\facade\Db;

/**
 * 环境检测规则
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class InstallUtil
{
    use JsonTrait;

    /**
     * 安装表结构
     * @param \support\Request $request
     */
    public function structure(Request $request)
    {
        $database = Cache::get('install_database');
        if (empty($database)) {
            return $this->fail('获取安装数据库配置失败');
        }
        // 安装索引
        $index = $request->get('total', 0);
        // 获取全部插件
        $data = Packages::getPackages($request->plugin, 'plugins');
        // 追加主应用插件
        $data = array_merge(['xbCode' => '小白基础应用'], $data);
        // 检测全部数据表是否安装完成
        if ($index >= count($data)) {
            return $this->success("全部插件安装完成...", [
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
            return $this->fail('获取插件失败');
        }
        // 获取插件安装类
        $class = "\\plugin\\{$plugin['name']}\\api\\Install";
        if (!class_exists($class)) {
            return $this->fail("{$plugin['title']}插件安装类不存在");
        }
        if (!method_exists($class, 'install')) {
            return $this->fail("{$plugin['title']}插件安装方法不存在");
        }
        // 获取插件配置
        $config = Packages::config($plugin['name']);
        // 连接数据库
        Mysql::connect($database);
        // 执行安装
        $class::install($config['version']);
        // 返回成功
        return $this->success("安装 【{$plugin['title']}】 插件成功...", [
            'next' => 'structure',
            'total' => $index + 1
        ]);
    }
    
    /**
     * 安装数据
     * @param \support\Request $request
     * @throws \Exception
     */
    public function database(Request $request)
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
            'web_desc' => $site['web_name'],
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
        return $this->success('安装数据完成...', [
            'next' => 'config',
        ]);
    }

    /**
     * 安装配置文件
     * @param \support\Request $request
     */
    public function config(Request $request)
    {
        // 获取参数
        $redis = Cache::get('install_redis',[]);
        $database = Cache::get('install_database',[]);
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
        // 重启框架
        FrameUtil::delayReload(2);
        // 返回成功
        return $this->success('应用安装成功，即将跳转...');
    }
}