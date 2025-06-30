<?php
namespace plugin\xbCode\app\admin\controller;

use support\Request;
use plugin\xbCode\api\Url;
use plugin\xbCode\builder\Builder;
use plugin\xbCode\XbController;

/**
 * 首页控制器
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class IndexController extends XbController
{
    /**
     * 客户端无需登录的方法
     * @var array
     */
    protected $noLogin = [
        'index',
        'site',
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
        return $this->adminView();
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
        // 获取站点配置
        // $config = ConfigApi::get('system', []);
        // // 获取版权配置
        // $copyright = ConfigApi::get('copyright', []);
        // if (!empty($copyright)) {
        //     $config['login_beian'] = [
        //         'system_name' => $config['web_name'] ?? '',
        //         'system_url' => $config['web_url'] ?? '',
        //         ...$copyright,
        //     ];
        // }
        // // 工具栏配置
        // $toolbar = xbUrl('Index/toolbar');
        // $config['public_view']['toolbar'] = $toolbar;
        // // 获取配置
        // $data = AppsEntry::get($config);
        $data = [
            'web_name' => '积木云网络',
            'web_url' => 'https://www.xbcode.net',
            'web_logo' => '',
            'web_desc' => '积木云是一个基于 Vue3 + Vite + Element-Plus 的后台管理系统模板，集成了多种常用功能和组件，帮助开发者快速搭建高效的管理系统。',
            'web_icp' => '粤ICP备2023000000号',
            'web_police' => '粤公网安备 4400000000000号',
            'web_police_code' => '4400000000000',
            'web_version' => '1.0.0',
            'about_name' => '贵州积木云网络科技有限公司',
            'about_url' => 'https://www.xbcode.net',
            'login_data' => [
                'login_title' => '总后台登录',
                'login_desc' => '欢迎使用积木云后台管理系统',
            ],
            'public_api' => [
                'captcha' => Url::make('publics/captcha'),
                'login' => xbUrl('publics/login'),
                'user' => xbUrl('publics/user'),
                'menus' => xbUrl('publics/menus'),
                'layouts' => xbUrl('publics/layouts'),
            ],
            'public_view' => [
                'workbench' => xbUrl('Index/workbench'),
                'toolbar' => xbUrl('Index/toolbar'),
                'user' => xbUrl('Admin/profile'),
            ],
            'upload_api' => [
                'upload' => Url::make('Upload/upload')->plugin('xbUpload')->slash(),
            ],
        ];
        return $this->successRes($data);
    }

    /**
     * 获取工具栏视图
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function toolbar(Request $request)
    {
        return $this->successRes(Builder::display());
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
        return $this->successRes(Builder::display());
    }
}
