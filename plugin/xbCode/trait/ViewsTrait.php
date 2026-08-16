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
     * @throws Exception
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
     * 渲染插件内APP模块视图
     * @param array $vars
     * @param string $file
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function viewAppView(array $vars = [], string $file = '')
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
        if(empty($file)) {
            // 模块名称
            $module = $option['module'] ?? request()->app;
            // 控制器方法
            $controller = request()->controller;
            $controller = str_replace('\\', '/', $controller);
            $controller = basename($controller);
            $controller = str_replace('Controller', '', $controller);
            // 驼峰转下划线
            $controller = toUnderScore($controller);
            // 方法名称
            $method = request()->action;
            // 文件地址
            $file = "app/{$module}/view/{$controller}/{$method}";
        }
        $query = request()->get();
        $vars = [
            ...$query,
            ...$vars,
        ];
        $display = self::viewVue($file, $vars, $option, [
            'height' => '100%',
            ...$amis
        ]);
        return $this->successRes($display);
    }
}