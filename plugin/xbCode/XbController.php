<?php
namespace plugin\xbCode;

use Exception;
use plugin\xbCode\builder\Builder;
use plugin\xbCode\utils\trait\JsonTrait;

/**
 * 控制器基类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class XbController
{
    // 引入JsonUtil
    use JsonTrait;

    /**
     * 构造方法
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function __construct()
    {
        // 初始化
        $this->init();
    }

    /**
     * 初始化方法
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function init()
    {
    }
    
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
    protected function view(string $file = '')
    {
        if(empty($file)){
            $file = request()->path();
            $control = str_replace('\\','/', request()->controller);
            $control = basename($control);
            $control = str_replace('Controller', '', $control);
            $control = strtolower($control);
            $method = request()->action;
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
    protected function display(string $file = '', array $vars = [], array $option = [], array $amis = [])
    {
        $display = Builder::display($file, $vars, $option, $amis);
        return $this->successRes($display);
    }
}