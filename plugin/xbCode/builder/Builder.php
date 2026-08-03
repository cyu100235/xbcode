<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder;

use Exception;
use plugin\xbCode\builder\Renders\XbVue;
use plugin\xbCode\builder\Renders\XbForm;
use plugin\xbCode\builder\Renders\XbCrud;
use plugin\xbCode\builder\Renders\XbTabForm;

/**
 * 渲染器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class Builder
{
    /**
     * 表格渲染器
     * @param callable $callback 组件参数
     * @return XbCrud
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function crud(callable $callback)
    {
        return XbCrud::make($callback);
    }

    /**
     * 表单渲染器
     * @param callable $callback 组件参数
     * @return XbForm
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function form(callable  $callback)
    {
        return XbForm::make($callback);
    }

    /**
     * 选项卡表单
     * @param callable $callback 组件参数
     * @return XbTabForm
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function tabForm(callable $callback)
    {
        return XbTabForm::make($callback);
    }

    /**
     * 获取远程组件
     * @param string $api
     * @param array $vars
     * @param array $option
     * @return XbVue
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function vue(string $api, array $vars = [], array $option = [])
    {
        return XbVue::make($api, $vars, $option);
    }
    
    /**
     * 渲染Vue组件
     * @param string $file
     * @param array $vars
     * @param array $option
     * @param array $amis
     * @throws \Exception
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function view(string $file, array $vars = [], array $option = [], array $amis = [])
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
     * 渲染当前应用视图页面
     * @param string $file
     * @param array $vars
     * @param array $option
     * @param array $amis
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function display(string $file = '', array $vars = [], array $option = [], array $amis = [])
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
        return static::view($file, $vars, $option, [
            'height' => '100%',
            ...$amis
        ]);
    }
}
