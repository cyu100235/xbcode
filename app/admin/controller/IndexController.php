<?php
namespace app\admin\controller;

use app\model\WebPlugin;
use support\Request;
use xbcode\providers\AppProvider;
use xbcode\XbController;

/**
 * 首页控制器
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class IndexController extends XbController
{
    /**
     * 不需要登录的方法
     * @var array
     */
    protected $noLogin = [
        'index',
        'site',
        'workbench',
    ];

    /**
     * 首页视图
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        $path = $request->path();
        if (!str_ends_with($path, '/')) {
            return redirect("{$path}/");
        }
        return $this->view('public/backend/index', 'html');
    }

    /**
     * 站点信息
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function site(Request $request)
    {
        $data = AppProvider::get();
        return $this->successRes($data);
    }

    /**
     * 工作台视图
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function workbench(Request $request)
    {
        // 获取已授权插件工作台路由
        $data = WebPlugin::getWorkbenchRoute();
        return $this->successRes($data);
    }
}
