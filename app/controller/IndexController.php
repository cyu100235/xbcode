<?php

namespace app\controller;

use app\common\utils\InstallUtil;
use app\common\XbController;
use support\Request;

/**
 * 首页控制器
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class IndexController extends XbController
{
    /**
     * 渲染后台视图
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        // 判断是否已经安装
        if (!InstallUtil::hasInstall()) {
            return redirect('/install/');
        }
        return view('index/index');
    }

    /**
     * 渲染欢迎视图
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function welcome(Request $request)
    {
        return view('index/welcome');
    }
}
