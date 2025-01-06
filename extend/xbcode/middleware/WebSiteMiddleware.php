<?php
namespace xbcode\middleware;

use Exception;
use app\model\WebSite;
use app\model\WebPlugin;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

/**
 * 租户站点中间件
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class WebSiteMiddleware implements MiddlewareInterface
{
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
        // 获取当前域名
        $domain = $request->host();
        // 获取域名对应站点
        $webSite = WebSite::getWebSiteByDomain($domain);
        // 检测域名站点不存在则跳转总后台
        if (empty($webSite)) {
            return redirect('/backend/');
        }
        // 检测站点是否启用
        if ($webSite['state'] !== '20') {
            throw new Exception('该站点已被关闭~', 403);
        }
        // 检测站点是否永久
        if (!empty($webSite['expire_time'])) {
            // 检测站点是否过期
            $expireTime = strtotime($webSite['expire_time']);
            if (time() > $expireTime) {
                throw new Exception('该站点已过期~', 403);
            }
        }
        // 设置站点APPID
        $request->saasAppid = $webSite['id'];
        // 验证本地插件租户站点授权
        $this->checkedLocalPluginAuth($request);

        /**
         * @var Response $response
         */
        $response = $next($request);

        // 返回响应
        return $response;
    }
    
    /**
     * 验证本地插件租户站点授权
     * @param \Webman\Http\Request $request
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function checkedLocalPluginAuth(Request $request)
    {
        // 获取插件名称
        $pluginName = $request->plugin;
        if (empty($pluginName)) {
            return;
        }
        // 获取站点插件字典
        $webPluginsDict = $this->getWebPluginsDict();
        // 获取站点ID
        $webSiteId = $request->saas_appid;
        // 获取授权插件信息
        $key = "{$pluginName}_{$webSiteId}";
        $plugin = $webPluginsDict[$key] ?? null;
        // 检测插件授权是否存在
        if (empty($plugin)) {
            throw new Exception('您没有该插件的授权~', 403);
        }
        // 插件永久不过期
        if (empty($plugin['expire_time'])) {
            return;
        }
        // 检测插件授权是否过期
        $expireTime = strtotime($plugin['expire_time']);
        if (time() > $expireTime) {
            throw new Exception('您的插件授权已过期~', 403);
        }
    }

    /**
     * 获取站点插件字典
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function getWebPluginsDict()
    {
        // 获取站点插件信息
        $data = WebPlugin::getWebAuthPluginAll();
        $list = [];
        foreach ($data as $value) {
            $list["{$value['name']}_{$value['saas_appid']}"] = $value;
        }
        return $list;
    }
}
