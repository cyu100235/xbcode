<?php

namespace app\admin\controller;

use app\common\BaseController;
use app\common\service\AppService;
use app\common\service\SystemService;
use think\Request;

class IndexController extends BaseController
{
    /**
     * 无需登录方法
     * @var array
     */
    protected $noLogin = ['index','site'];

    /**
     * 渲染后台视图
     * @param \think\Request $request
     * @return string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        # 渲染视图
        return getViewContent('view');
    }
    
    /**
     * 应用入口
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function site(Request $request)
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

    /**
     * 获取系统信息
     * @param \think\Request $request
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function info(Request $request)
    {
        $systemInfo = SystemService::info();
        $data       = [
            'web_name'          => $systemInfo['web_name'],
            'web_logo'          => $systemInfo['logo'],
            'about_name'        => $systemInfo['name'],
            'version_name'      => $systemInfo['version_name'],
            'version'           => $systemInfo['version'],
        ];
        return $this->successRes($data);
    }
}
