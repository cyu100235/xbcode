<?php
namespace app\admin\controller;

use support\Request;
use app\model\WebPlugin;
use xbcode\XbController;
use xbcode\providers\AppProvider;

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
        'toolbar',
        'toolbarView',
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
        $config = [
            'public_view' => [
                'toolbar' => xbUrl('Index/toolbar'),
            ],
        ];
        // 获取站点配置
        $data = AppProvider::get($config);
        // 返回数据
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
        // 返回数据
        return $this->successRes($data);
    }

    /**
     * 工具栏数据
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function toolbar(Request $request)
    {
        // 获取已授权插件工作台路由
        $data = WebPlugin::getToolbarRoute();
        $data = array_merge($data, [
            'admin' => xbUrl('Index/toolbarView'),
        ]);
        // 返回数据
        return $this->successRes($data);
    }

    /**
     * 工具栏视图
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function toolbarView(Request $request)
    {
        return $this->view('view/admin/toolbar');
    }
}
