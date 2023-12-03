<?php

namespace app\index\controller;

use app\common\BaseController;

class IndexController extends BaseController
{
    /**
     * 首页
     * @return \think\response\Redirect
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index()
    {
        # 跳转后台
        return redirect('/admin/');
    }
}
