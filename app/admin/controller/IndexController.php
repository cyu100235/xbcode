<?php

namespace app\admin\controller;

use app\common\BaseController;
use app\common\service\AppService;

class IndexController extends BaseController
{
    /**
     * 无需登录方法
     * @var array
     */
    protected $NotLogin = ['index','site'];

    /**
     * 渲染后台视图
     * @return \think\response\Redirect|string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index()
    {
        # 渲染视图
        return getViewContent('view');
    }

    /**
     * 应用入口
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function site()
    {
        // 返回数据
        $data       = [
            'web_name'              => 'XB-Base',
            'web_title'             => '后台登录',
            'web_logo'              => '',
        ];
        $data = AppService::resutl($data);
        return $this->successRes($data);
    }
}
