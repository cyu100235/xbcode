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
namespace plugin\xbCode\builder;

use Exception;
use plugin\xbCode\builder\Renders\Vue;
use plugin\xbCode\builder\Renders\Form;
use plugin\xbCode\builder\Renders\Grid;

/**
 * 渲染器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class Builder
{
    /**
     * 表格渲染器
     * @param callable $func
     * @param string $api
     * @return Grid
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function crud(callable $func, string $api = '')
    {
        $api = static::getApi($api);
        $api = "{$api}?_act=1";
        return Grid::make($api, $func);
    }

    /**
     * 表单渲染器
     * @param callable $func
     * @param string $api
     * @return Form
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function form(callable $func, string $api = '')
    {
        $api = static::getApi($api);
        $dialog = (int) request()->get('_dialog', 0) ? true : false;
        return Form::make($func, $api)->dialog($dialog);
    }

    /**
     * 获取远程组件
     * @param string $api
     * @param array $vars
     * @param array $option
     * @return Vue
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function vue(string $api, array $vars = [], array $option = [])
    {
        return Vue::make($api, $vars, $option);
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
            throw new Exception("视图文件不存在：{$file}");
        }
        $content = file_get_contents($path);
        if (empty($content)) {
            throw new Exception("视图文件内容为空：{$file}");
        }
        // 渲染器
        $result = Vue::view($content, $vars, $amis);
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
            // 全部转小写
            $controller = strtolower($controller);
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

    /**
     * 获取接口地址
     * @param string $api
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function getApi(string $api): string
    {
        if (empty($api)) {
            $api = request()->path();
        }
        return $api;
    }
}
