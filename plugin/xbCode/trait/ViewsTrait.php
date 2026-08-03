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
use plugin\xbCode\builder\Builder;

/**
 * 视图处理
 * @author 楚羽幽 958416459@qq.com
 * @copyright 贵州积木云网络科技有限公司
 */
trait ViewsTrait
{
    
    /**
     * 渲染后台视图
     * @throws Exception
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function adminView()
    {
        $path = request()->path();
        if (!str_ends_with($path, '/')) {
            return redirect("{$path}/");
        }
        $viewPath = base_path() . '/plugin/xbCode/public/backend/index.html';
        if(!file_exists($viewPath)){
            throw new Exception("后台视图文件不存在：{$viewPath}");
        }
        $content = file_get_contents($viewPath);
        if (empty($content)) {
            throw new Exception("后台视图文件内容为空：{$viewPath}");
        }
        return response($content)->withHeader('Content-Type', 'text/html; charset=utf-8');
    }

    /**
     * 渲染视图文件
     * @param string $file
     * @throws \Exception
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function viewPage(string $file = '')
    {
        if(empty($file)){
            $file = request()->path();
            $control = str_replace('\\','/', request()->controller);
            $control = basename($control);
            $control = str_replace('Controller', '', $control);
            // 驼峰转下划线
            $control = toUnderScore($control);
            // 方法名称
            $method = request()->action;
            // 模块名称
            $module = request()->app;
            $module = "{$module}/";
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
     * 渲染Vue视图
     * @param string $file
     * @param array $vars
     * @param array $option
     * @param array $amis
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function display(array $vars = [], string $file = '', array $option = [], array $amis = [])
    {
        $display = Builder::display($file, $vars, $option, $amis);
        return $this->successRes($display);
    }
}