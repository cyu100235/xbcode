<?php

namespace app\admin\controller;

use app\common\BaseController;
use app\common\service\CloudService;
use app\common\service\SystemService;
use think\Request;

/**
 * 云服务中心
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class CloudController extends BaseController
{
    /**
     * 无需登录方法
     * @var array
     */
    protected $noLogin = ['captcha','login'];
     
    /**
     * 用户登录
     * @param \think\Request $request
     * @return array|mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function login(Request $request)
    {
        $username = $request->post('username','');
        $password = $request->post('password','');
        $scode = $request->post('scode','');
        return CloudService::login($username, $password,$scode);
    }
    
    /**
     * 获取验证码
     * @param \think\Request $request
     * @return \yzh52521\EasyHttp\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function captcha(Request $request)
    {
        return CloudService::captcha();
    }

    /**
     * 用户信息
     * @param \think\Request $request
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function user(Request $request)
    {
    }
    
    /**
     * 系统信息
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function info(Request $request)
    {
        $data = SystemService::info();
        return $this->successRes($data);
    }
}
