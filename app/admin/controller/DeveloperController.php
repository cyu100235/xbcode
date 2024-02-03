<?php

namespace app\admin\controller;

use app\common\BaseController;
use app\common\service\CloudService;
use think\Request;

class DeveloperController extends BaseController
{
    /**
     * 开发者应用列表
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        return CloudService::getAuthorAppList();
    }

    /**
     * 开发者模式
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function getDeveloperMode(Request $request)
    {
        if ($request->isPost()) {
            $developerMode = $request->post('developerMode','');
            CloudService::developerMode($developerMode);
            return $this->success('切换模式成功');
        }
        $data = [
            'developerMode'  => CloudService::developerMode()
        ];
        return $this->successRes($data);
    }

    /**
     * 发布代码
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function publish(Request $request)
    {
        return $this->successRes([]);
    }
}
