<?php
namespace app\backend\controller;

use app\model\Plugins;
use support\Request;
use xbcode\service\xbcode\PluginService;
use xbcode\XbController;
use xbcode\service\xbcode\FrameService;
use xbcode\service\xbcode\SiteService;
use xbcode\service\xbcode\UserService;
use xbcode\providers\update\UpdateProvider;

/**
 * 总后台首页控制器
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class ServerController extends XbController
{
    /**
     * 服务端无需登录的方法
     * @var array
     */
    protected $server = [
        'login',
        'register',
        'password',
    ];

    /**
     * 服务端无需验证授权的方法
     * @var array
     */
    protected $serverAuth = [
        'update',
        'version',
        'authorize',
    ];

    /**
     * 授权登录
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function login(Request $request)
    {
        if ($request->method() === 'POST') {
            // 获取数据
            $username = (string) $request->post('username', '');
            $password = (string) $request->post('password', '');
            // 用户登录
            UserService::login($username, $password);
            // 返回数据
            return $this->success('登录成功');
        }
        // 渲染视图
        return $this->view('view/backend/login');
    }

    /**
     * 用户注册
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function register(Request $request)
    {
        if ($request->method() === 'POST') {
            return $this->success('用户注册成功');
        }
        // 渲染视图
        return $this->view('view/backend/register');
    }

    /**
     * 找回密码
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function password(Request $request)
    {
        if ($request->method() === 'POST') {
            return $this->success('密码找回成功');
        }
        // 渲染视图
        return $this->view('view/backend/password');
    }

    /**
     * 系统更新
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function update(Request $request)
    {
        if ($request->method() === 'POST') {
            $versionName = (string) $request->post('version_name', '');
            $version     = (int) $request->post('version', '');
            $step        = (string) $request->post('step', '');
            if (empty($versionName)) {
                return $this->fail('更新版本名称参数错误');
            }
            if (empty($version)) {
                return $this->fail('更新版本号参数错误');
            }
            if (empty($step)) {
                return $this->fail('更新步骤参数错误');
            }
            // 开始更新
            $service = new UpdateProvider;
            return $service->start($versionName, $version, $step);
        }
        // 渲染视图
        return $this->view('view/backend/update');
    }

    /**
     * 检测版本更新
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function checked(Request $request)
    {
        $checked = FrameService::checked();
        $message = $checked ? '发现新版本' : '当前已是最新版本';
        return $this->successFul($message, [
            'status' => $checked,
        ]);
    }

    /**
     * 获取框架版本信息
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function version(Request $request)
    {
        // 本地版本
        $projects = config('projects', '1.0.0');
        // 获取框架版本信息
        $result = FrameService::version($projects['version']);
        // 服务端版本名称
        $serverVersionName = $result['data']['version_name'] ?? '';
        $serverVersion     = $result['data']['version'] ?? '';
        // 返回数据
        $data = [
            'status' => version_compare($serverVersionName, $projects['version_name'], '>'),
            'is_new' => $projects['version'] >= $serverVersion,
            'detail' => [
                'create_at' => $result['data']['create_at'] ?? '',
                'local_version_name' => $projects['version_name'] ?? '',
                'local_version' => $projects['version'] ?? 0,
                'new_version_name' => $serverVersionName,
                'new_version' => $serverVersion,
                'content' => $result['data']['content'] ?? '',
            ]
        ];
        return $this->successRes($data);
    }

    /**
     * 获取插件版本信息
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function plugins(Request $request)
    {
        // 获取插件版本更新信息
        $result = PluginService::checked();
        // 组合HTML更新内容
        $content = array_map(function ($item) {
            return "<p>插件名称：{$item['title']} 最新版本{$item['version_name']}，本地版本{$item['local_version_name']}</p>";
        }, $result);
        $content = implode('', $content);
        // 当前版本KEY
        $versionKey = array_map(function ($item) {
            return "{$item['name']}_{$item['version']}";
        }, $result);
        $versionKey = implode('_', $versionKey);
        // 返回数据
        $data = [
            'status' => count($result) > 0,
            'detail' => [
                'version_key' => $versionKey,
                'content' => $content,
            ]
        ];
        return $this->successRes($data);
    }

    /**
     * 授权信息
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function authorize(Request $request)
    {
        if ($request->method() === 'POST') {
            $data                   = SiteService::detail();
            $data['system_name']    = config('projects.title');
            $data['system_version'] = config('projects.version');
            return $this->successRes($data);
        }
        // 渲染视图
        return $this->view('view/backend/authorize');
    }
}
