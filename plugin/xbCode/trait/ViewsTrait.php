<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\trait;

use Exception;
use plugin\xbCode\builder\Renders\XbVue;

/**
 * 视图处理
 * @author 楚羽幽 958416459@qq.com
 * @copyright 贵州积木云网络科技有限公司
 */
trait ViewsTrait
{
    /**
     * 渲染视图文件
     * @param string $file
     * @throws Exception
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function viewPage(string $file = '')
    {
        if (empty($file)) {
            $control = str_replace('\\', '/', request()->controller);
            $control = basename($control);
            $control = str_replace('Controller', '', $control);
            if (function_exists('toUnderScore')) {
                $control = toUnderScore($control);
            }
            $method = request()->action;
            $module = request()->app;
            $module = $module ? "{$module}/" : '';
            $file = "app/{$module}view/{$control}/{$method}";
        }
        $plugin = request()->plugin;
        $shortPath = "/plugin/{$plugin}/{$file}.html";
        $viewPath = base_path() . $shortPath;
        if (!file_exists($viewPath)) {
            throw new Exception("视图文件不存在：{$shortPath}");
        }
        $content = file_get_contents($viewPath);
        if (empty($content)) {
            throw new Exception("视图文件内容为空：{$viewPath}");
        }
        return response($content)->withHeader('Content-Type', 'text/html; charset=utf-8');
    }

    /**
     * 渲染插件内APP模块视图
     * @param array $vars
     * @param string $file
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function viewAppView(array $vars = [], string $file = '')
    {
        if (empty($file)) {
            $control = str_replace('\\', '/', request()->controller);
            $control = basename($control);
            $control = str_replace('Controller', '', $control);
            if (function_exists('toUnderScore')) {
                $control = toUnderScore($control);
            }
            $method = request()->action;
            $file = "{$control}/{$method}";
        }
        return view($file, $vars);
    }

    /**
     * 渲染Vue组件
     * @param string $file
     * @param array $vars
     * @param array $option
     * @param array $amis
     * @throws Exception
     * @return XbVue
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function viewVue(string $file, array $vars = [], array $option = [], array $amis = [])
    {
        // 获取插件名称
        $plugin = $option['plugin'] ?? request()->plugin;
        // 获取文件后缀
        $suffix = $option['suffix'] ?? 'vue';
        // 拼接文件地址
        $file = "plugin/{$plugin}/{$file}";
        // 模板文件
        $template = base_path() . "/{$file}";
        // 拼接文件名得到完整地址
        $path = "{$template}.{$suffix}";
        // 获取视图内容
        if (!file_exists($path)) {
            throw new Exception("视图文件不存在：{$file}.{$suffix}");
        }
        $content = file_get_contents($path);
        if (empty($content)) {
            throw new Exception("视图文件内容为空：{$file}.{$suffix}");
        }
        // 渲染器
        $result = XbVue::view($content, $vars, $amis);
        // 返回实例
        return $result;
    }
}