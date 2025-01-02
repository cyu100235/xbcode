<?php
namespace app\backend\controller;

use support\Request;
use xbcode\providers\AppProvider;
use xbcode\providers\ConfigProvider;
use xbcode\XbController;

/**
 * 首页控制器
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class IndexController extends XbController
{
    /**
     * 服务端无需登录的方法
     * @var array
     */
    protected $server = [
        'index',
        'site',
        'workbench',
    ];

    /**
     * 客户端无需登录的方法
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
        try {
            $config    = ConfigProvider::get('system', '', [], [
                'refresh' => true,
            ]);
            $copyright = ConfigProvider::get('copyright', '', []);
            if (!empty($copyright)) {
                $config['login_beian'] = $copyright;
            }
            $data = AppProvider::get($config);
            return $this->successRes($data);
        } catch (\Throwable $th) {
            // 检测数据库连接失败
            $errorCode = [
                1040,
                1041,
                1042,
                1043,
                1044,
                1045,
                10501,
            ];
            if (in_array($th->getCode(), $errorCode)) {
                return $this->fail('数据库连接失败，请检查数据库配置');
            }
            return $this->fail($th->getMessage());
        }
    }

    /**
     * 获取工作台远程视图
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function workbench(Request $request)
    {
        return $this->view('view/backend/workbench');
    }
}
