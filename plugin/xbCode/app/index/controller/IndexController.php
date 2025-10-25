<?php
namespace plugin\xbCode\app\index\controller;

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
     * 首页
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function index(Request $request)
    {
        $adminModule = env('ADMIN_URL', 'backend');
        return redirect("/{$adminModule}/");
    }
}
