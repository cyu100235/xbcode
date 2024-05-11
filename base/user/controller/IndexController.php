<?php

namespace base\user\controller;

use app\XbController;
use support\Request;

class IndexController extends XbController
{
    /**
     * 插件默认接口
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        return $this->success('Hello, World!');
    }
}
