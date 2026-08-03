<?php
/**
 * 积木云渲染器
 * @package  XbCode.0
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode;

use plugin\xbCode\trait\JsonTrait;
use plugin\xbCode\trait\ViewsTrait;
use plugin\xbCode\trait\ConfigTrait;
use plugin\xbCode\trait\FieldsTrait;

/**
 * 控制器基类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class XbController
{
    // 引入工具类
    use JsonTrait;
    use ViewsTrait;
    use ConfigTrait;
    use FieldsTrait;

    /**
     * 构造方法
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function __construct()
    {
        // 初始化
        $this->init();
    }

    /**
     * 初始化方法
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function init()
    {
    }

    /**
     * 获取控制器名称(下划线命名)
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getController()
    {
        $suffix = config('app.controller_suffix', 'Controller');
        $controller = request()->controller;
        $controller = str_replace('\\', '/', $controller);
        $controller = basename($controller);
        $controller = str_replace($suffix, '', $controller);
        $controller = toUnderScore($controller);
        return $controller;
    }

    /**
     * 获取方法名称(下划线命名)
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getAction()
    {
        $action = request()->action;
        $action = toUnderScore($action);
        return $action;
    }

    /**
     * 获取控制器与方法名称(下划线命名)
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function getRoutePath()
    {
        $controller = $this->getController();
        $action = $this->getAction();
        $routePath = "{$controller}/{$action}";
        return $routePath;
    }

    /**
     * 获取完整路由地址（模块/控制器/方法）
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function getRouteFullPath()
    {
        $module = request()->app;
        $routePath = $this->getRoutePath();
        if ($module) {
            $routePath = "{$module}/{$routePath}";
        }
        return $routePath;
    }
}