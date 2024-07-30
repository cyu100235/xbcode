<?php

namespace app\controller;

use app\common\providers\RouteProvider;
use app\common\XbController;
use support\Request;

/**
 * 文档控制器
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class ApidocController extends XbController
{
    /**
     * 渲染文档视图
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        if (!str_ends_with($request->path(), '/')) {
            return redirect('/apidoc/');
        }
        if (!RouteProvider::downloadView('apidoc-view')) {
            throw new \Exception('下载文档视图失败');
        }
        return $this->adminView('runtime/apidoc-view/index');
    }
}
