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
namespace plugin\xbCode\app\middleware;

use Exception;
use Webman\Http\Request;
use Webman\Http\Response;
use plugin\xbCode\api\Install;
use Webman\MiddlewareInterface;
use plugin\xbCode\api\PluginsApi;

/**
 * 插件检测
 * @copyright 贵州积木云网络网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginMiddleware implements MiddlewareInterface
{
    /**
     * 处理请求
     * @param \Webman\Http\Request $request
     * @param callable $handler
     * @return \Webman\Http\Response
     * @copyright 贵州积木云网络网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function process(Request $request, callable $handler): Response
    {
        // 插件业务验证处理
        $this->pluginValidate($request);
        // 继续向洋葱芯穿越，直至执行控制器得到响应
        $response = $handler($request);
        // 返回响应
        return $response;
    }

    /**
     * 插件业务验证处理
     * @param \Webman\Http\Request $request
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function pluginValidate(Request $request)
    {
        // 检测是否安装
        if (!Install::checked()) {
            return;
        }
        // 获取插件标识
        $pluginNmae = $request->plugin;
        if (empty($pluginNmae)) {
            return;
        }
        $uri = $request->uri();
        if (str_contains($uri, 'preview.svg')) {
            return;
        }
        // 检测插件是否存在
        if (!PluginsApi::make()->make()->exists($pluginNmae)) {
            throw new Exception("插件 {$pluginNmae} 不存在");
        }
        // 检测是否已安装
        if (!PluginsApi::make()->make()->installed($pluginNmae)) {
            throw new Exception("该插件 {$pluginNmae} 未安装");
        }
        // 检测插件是否已启用
        if (!PluginsApi::make()->make()->hasEnabled($pluginNmae)) {
            throw new Exception("该插件 {$pluginNmae} 未启用");
        }
    }
}