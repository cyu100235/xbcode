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
    protected $noLogin = ['captcha', 'login'];

    /**
     * 用户登录
     * @param \think\Request $request
     * @return array|mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function login(Request $request)
    {
        $username = $request->post('username', '');
        $password = $request->post('password', '');
        return CloudService::login($username, $password);
    }

    /**
     * 用户信息
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function user(Request $request)
    {
        $data = CloudService::user();
        if (!isset($data['code'])) {
            return $this->fail('云端返回数据格式错误');
        }
        if ($data['code'] !== 200) {
            return $this->successRes($data);
        }
        return $this->successRes($data['data']);
    }

    /**
     * 在线充值
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function recharge(Request $request)
    {
        $money = (float)$request->post('money','');
        $payType = $request->post('pay_type','');
        $data = CloudService::recharge($money,$payType);
        if (!isset($data['code'])) {
            return $this->fail('云端返回数据格式错误');
        }
        if ($data['code'] !== 200) {
            return $this->failFul($data['msg'], $data['code']);
        }
        return $this->successRes($data['data'] ?? []);
    }

    /**
     * 获取充值二维码
     * @param \think\Request $request
     * @return array|mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function getRechargeQrcode(Request $request)
    {
        $order_no = $request->get('order_no','');
        $payType = $request->get('pay_type','');
        $data = CloudService::getRechargeQrcode($order_no,$payType);
        return $data;
    }

    /**
     * 获取账单
     * @param \think\Request $request
     * @return array|mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function bill(Request $request)
    {
        $page  = (int) $request->get('page', 1);
        $limit = (int) $request->get('limit', 20);
        return CloudService::getBill($page, $limit);
    }

    /**
     * 获取公告
     * @param \think\Request $request
     * @return array|mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function notice(Request $request)
    {
        return CloudService::getNotice();
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
