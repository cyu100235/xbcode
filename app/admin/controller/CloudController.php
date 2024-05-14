<?php

namespace app\admin\controller;

use app\service\CloudSerivce;
use app\XbController;
use support\Request;

class CloudController extends XbController
{
    /**
     * 无需登录方法
     * @var array
     */
    protected $noLogin = [
        'config',
        'login',
    ];

    /**
     * 获取云服务配置项
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function config(Request $request)
    {
        return $this->successRes('清除缓存成功');
    }

    /**
     * 云服务用户登录
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function login(Request $request)
    {
        return CloudSerivce::login($request);
    }

    /**
     * 获取用户信息
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        return CloudSerivce::userInfo($request);
    }
}
