<?php
namespace plugin\{PLUGIN_NAME}\app\home\controller;

use support\Request;
use plugin\xbCode\XbController;

/**
 * 首页控制器
 * @copyright 贵州积木云网络网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class IndexController extends XbController
{
    /**
     * 默认首页
     * @return \support\Response
     * @copyright 贵州积木云网络网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function index(Request $request)
    {
        return view('index/index', ['name' => '{PLUGIN_NAME}']);
    }
    
    /**
     * 工作台
     * @return \support\Response
     * @copyright 贵州积木云网络网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    // public function workbench()
    // {
    //     return $this->view('view/workbench');
    // }
}