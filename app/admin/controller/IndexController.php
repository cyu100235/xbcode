<?php

namespace app\admin\controller;

use app\common\providers\ConfigProvider;
use app\common\providers\AppProvider;
use app\common\XbController;
use support\Request;

class IndexController extends XbController
{
    /**
     * 无需登录方法
     * @var array
     */
    protected $noLogin = [
        'index',
        'site'
    ];

    /**
     * 渲染后台视图
     * @param \support\Request $request
     * @return string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        // 渲染视图
        return $this->adminView();
    }

    /**
     * 应用入口
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function site(Request $request)
    {
        // 获取配置
        $config = ConfigProvider::get('system', '', []);
        // 返回数据
        $data = [
            'web_name' => $config['web_name'] ?? 'XB-Admin',
            'web_title' => '后台登录',
            'web_logo' => $config['web_logo'] ?? '',
            'public_api_login' => xbUrl('Login/login'),
            'public_api_user' => xbUrl('Login/user'),
            'public_api_user_edit_path' => xbUrl('Admin/info'),
            'public_api_user_edit' => xbUrl('user/info'),
        ];
        $data = AppProvider::resutl($data);
        return $this->successRes($data);
    }

    /**
     * 清除缓存
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function clear(Request $request)
    {
        return $this->successRes('清除缓存成功');
    }

    /**
     * 锁定屏幕
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function lock(Request $request)
    {
        return $this->successRes('锁定屏幕');
    }

    /**
     * 解锁屏幕
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function unlock(Request $request)
    {
        return $this->successRes('解锁屏幕');
    }
}
