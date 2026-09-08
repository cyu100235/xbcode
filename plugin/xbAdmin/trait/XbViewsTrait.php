<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\trait;

use Exception;
use plugin\xbCode\builder\Renders\XbVue;

/**
 * 后台视图处理
 */
trait XbViewsTrait
{
    /**
     * 渲染后台单页应用入口
     *
     * 前端产物固定在 plugin/xbAdmin/public/backend，
     * 其 vite base 需配置为 /app/xbAdmin/backend/，
     * 静态资源由 webman 的 /app/{插件}/{路径} 规则映射到插件 public 目录。
     * @param string $file 相对 backend 目录的入口文件
     * @throws Exception
     * @return \support\Response
     */
    protected function adminView(string $file = 'index.html')
    {
        $viewPath = base_path() . "/plugin/xbAdmin/public/backend/{$file}";
        if (!is_file($viewPath)) {
            throw new Exception("后台视图文件不存在：/plugin/xbAdmin/public/backend/{$file}", 404);
        }
        $content = (string) file_get_contents($viewPath);
        if ($content === '') {
            throw new Exception("后台视图文件内容为空：/plugin/xbAdmin/public/backend/{$file}", 500);
        }
        return response($content)->withHeader('Content-Type', 'text/html; charset=utf-8');
    }

    /**
     * 渲染插件内的静态HTML视图
     * @param string $file 相对插件根目录的文件路径，不带扩展名；留空按当前模块/控制器/方法推导
     * @throws Exception
     * @return \support\Response
     */
    protected function viewPage(string $file = '')
    {
        if ($file === '') {
            $file = $this->getViewFile();
        }
        $plugin = request()->plugin ?: 'xbAdmin';
        $shortPath = "/plugin/{$plugin}/{$file}.html";
        $viewPath = base_path() . $shortPath;
        if (!is_file($viewPath)) {
            throw new Exception("视图文件不存在：{$shortPath}", 404);
        }
        $content = (string) file_get_contents($viewPath);
        if ($content === '') {
            throw new Exception("视图文件内容为空：{$shortPath}", 500);
        }
        return response($content)->withHeader('Content-Type', 'text/html; charset=utf-8');
    }

    /**
     * 渲染远程Vue组件
     * @param string $file 相对插件根目录的视图文件，不带扩展名
     * @param array $vars 传递给组件的变量
     * @param array $option 渲染选项，支持 plugin、suffix
     * @param array $amis 组件属性
     * @throws Exception
     * @return XbVue
     */
    protected static function viewVue(string $file, array $vars = [], array $option = [], array $amis = [])
    {
        $plugin = $option['plugin'] ?? (request()->plugin ?: 'xbAdmin');
        $suffix = $option['suffix'] ?? 'vue';
        $shortPath = "/plugin/{$plugin}/{$file}.{$suffix}";
        $viewPath = base_path() . $shortPath;
        if (!is_file($viewPath)) {
            throw new Exception("视图文件不存在：{$shortPath}", 404);
        }
        $content = (string) file_get_contents($viewPath);
        if ($content === '') {
            throw new Exception("视图文件内容为空：{$shortPath}", 500);
        }
        return XbVue::view($content, $vars, $amis);
    }

    /**
     * 输出远程视图接口数据
     * @param array $vars
     * @param string $file
     * @param array $option
     * @param array $amis
     * @return \support\Response
     */
    protected function display(array $vars = [], string $file = '', array $option = [], array $amis = [])
    {
        if ($file === '') {
            $file = $this->getViewFile();
        }
        $vars = array_merge((array) request()->get(), $vars);
        $amis = array_merge(['height' => '100%'], $amis);
        $display = static::viewVue($file, $vars, $option, $amis);
        return $this->successRes($display);
    }

    /**
     * 按当前请求推导视图文件位置
     *
     * 控制器 IndexController、方法 workbench 对应 app/admin/view/index/workbench.vue，
     * 与视图文件的存放目录保持一致。
     * @return string
     */
    protected function getViewFile(): string
    {
        $module = (string) (request()->app ?: '');
        $controller = basename(str_replace('\\', '/', (string) request()->controller));
        $suffix = (string) config('app.controller_suffix', 'Controller');
        if ($suffix !== '') {
            $controller = str_replace($suffix, '', $controller);
        }
        $controller = toUnderScore($controller);
        $method = (string) request()->action;
        $directory = $module === '' ? 'app/view' : "app/{$module}/view";
        return "{$directory}/{$controller}/{$method}";
    }
}
