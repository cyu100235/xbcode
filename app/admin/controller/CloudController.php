<?php

namespace app\admin\controller;

use app\common\BaseController;

/**
 * 云服务中心
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class CloudController extends BaseController
{
    /**
     * 渲染后台视图
     * @return string
     * @author 楚羽幽
     */
    public function index()
    {
        return getViewContent('admin');
    }
}
