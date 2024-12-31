<?php
namespace xbcode\middleware;

use Exception;
use support\Cache;
use app\model\Plugins;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;
use xbcode\providers\plugins\PluginsUninstallProvider;

/**
 * 插件授权中间件
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginMiddleware implements MiddlewareInterface
{
    /**
     * 插件模型
     * @var Plugins
     */
    private $model;

    /**
     * 处理请求
     * @param \Webman\Http\Request $request
     * @param callable $next
     * @return \Webman\Http\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function process(Request $request, callable $next): Response
    {
        // 插件标识
        $pluginName = $request->plugin;
        // 检测插件是否已安装
        $this->model = new Plugins;
        // 获取插件缓存
        $plugins = $this->model->pluginCacheDict();
        // 获取插件信息
        $plugin = $plugins[$pluginName] ?? null;
        if (empty($plugin)) {
            throw new Exception('该插件未安装');
        }
        // 该插件是否启用
        if ($plugin['state'] != '20') {
            throw new Exception('该插件未启用');
        }
        // 插件标识
        $name = $plugin['name'];
        // 版本名称
        $versionName = $plugin['version_name'];
        // 版本编号
        $version = $plugin['version'];
        // 插件缓存KEY
        $this->pluginCacheKey = "xb_plugin_auth_{$name}_{$versionName}_{$version}";
        // 处理服务端插件授权
        $this->checkedServerPluginAuth($request, $plugin);
        /**
         * 如果是options请求则返回一个空响应，否则继续向洋葱芯穿越，并得到一个响应
         * @var Response $response
         */
        $response = $next($request);

        // 返回响应
        return $response;
    }

    /**
     * 处理服务端插件授权
     * @param \Webman\Http\Request $request
     * @param array $plugin
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function checkedServerPluginAuth(Request $request, array $plugin)
    {
        // 插件标识
        $name = $plugin['name'];
        // 版本名称
        $versionName = $plugin['version_name'];
        // 版本编号
        $version = $plugin['version'];
        // 获取授权信息
        $result = $this->model->getAuthData($name, $versionName, $version);
        if (empty($result['auth_key'])) {
            throw new Exception('获取插件授权密钥失败');
        }
        // 检测授权密钥KEY是否一致
        if ($result['auth_key'] != $plugin['auth_key']) {
            $this->throwPlugin($name, $versionName, $version);
        }
    }

    /**
     * 抛出插件异常
     * @param string $name 插件标识
     * @param string $versionName 版本名称
     * @param string $version 版本编号
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function throwPlugin(string $name, string $versionName, string $version)
    {
        // 实例插件卸载服务
        $service = new PluginsUninstallProvider;
        // 卸载插件数据库
        $service->start('database', $name, $versionName, $version);
        // 卸载插件文件
        $service->start('delCode', $name, $versionName, $version);
        // 卸载安装记录
        $service->start('success', $name, $versionName, $version);
        // 删除插件缓存
        Cache::delete(Plugins::getPluginCacheKey($name, $versionName, $version));
        // 抛出异常
        throw new Exception('插件授权密钥不一致');
    }
}
