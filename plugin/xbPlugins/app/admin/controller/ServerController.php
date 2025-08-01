<?php
namespace plugin\xbPlugins\app\admin\controller;

use support\Request;
use plugin\xbCode\XbController;
use plugin\xbPlugins\service\xbcode\api\UserService;

/**
 * 官方服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class ServerController extends XbController
{
    /**
     * 用户登录
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function login(Request $request)
    {
        return $this->display();
    }

    /**
     * 获取登录二维码/检查登录状态
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function qrcode(Request $request)
    {
        $code = $request->get('code');
        if (empty($code)) {
            $data = UserService::loginQrcode();
            return $this->successRes($data);
        }
        $data = UserService::checkeLogin($code);
        if (empty($data['id'])) {
            return $this->successRes($data);
        }
        return $this->successRes([
            'state' => 1,
        ]);
    }

    /**
     * 用户中心
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function user(Request $request)
    {
        return $this->display();
    }

    /**
     * 用户信息
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function userinfo(Request $request)
    {
        $data = UserService::userinfo();
        if (empty($data)) {
            return $this->successRes([]);
        }
        return $this->successRes($data);
    }

    /**
     * 用户退出
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function logout(Request $request)
    {
        UserService::logout();
        return $this->success('退出成功');
    }
}
