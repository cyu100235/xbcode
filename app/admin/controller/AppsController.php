<?php

namespace app\admin\controller;

use app\common\BaseController;
use xbCloud\CloudService;
use app\common\utils\apps\InstallUtil;
use app\common\utils\apps\UninstallUtil;
use app\common\utils\apps\UpdateUtil;
use think\Request;

/**
 * 应用市场
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class AppsController extends BaseController
{
    /**
     * 获取应用列表
     * @param \think\Request $request
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        $page     = (int) $request->get('page', 1);
        $limit    = (int) $request->get('limit', 20);
        $keyword  = $request->get('keyword', '');
        $category = $request->get('category', '');
        $platform = $request->get('platform', '');
        $install  = $request->get('install', '');
        $params   = [
            'page' => $page,
            'limit' => $limit,
            'keyword' => $keyword,
            'category' => $category,
            'platform' => $platform,
            'install' => $install,
        ];
        return CloudService::getAppList($params);
    }

    /**
     * 购买应用
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function buy(Request $request)
    {
        $appName = $request->get('app_name', '');
        $version = $request->get('version', 0);
        return CloudService::buyApp((string) $appName, (int) $version);
    }

    /**
     * 安装应用
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function install(Request $request)
    {
        $step    = $request->get('step', '');
        $appName = $request->get('app_name', '');
        $version = (int) $request->get('version', 0);
        if (!in_array($step, ['download', 'unzip', 'updateData', 'success'])) {
            return $this->fail('安装步骤错误');
        }
        if (empty($appName)) {
            return $this->fail('应用名称参数错误');
        }
        if (empty($version)) {
            return $this->fail('应用版本参数错误');
        }
        $class = new InstallUtil($request, $appName, $version);
        return call_user_func([$class, $step]);
    }

    /**
     * 更新应用
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function update(Request $request)
    {
        $step    = $request->get('step', '');
        $appName = $request->get('app_name', '');
        $version = (int) $request->get('version', 0);
        if (!in_array($step, ['download', 'backCode', 'backSql', 'deleteCode', 'unzip', 'updateData', 'success'])) {
            return $this->fail('更新步骤错误');
        }
        if (empty($appName)) {
            return $this->fail('应用名称参数错误');
        }
        if (empty($version)) {
            return $this->fail('应用版本参数错误');
        }
        $class = new UpdateUtil($request, $appName, $version);
        return call_user_func([$class, $step]);
    }

    /**
     * 卸载应用
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function uninstall(Request $request)
    {
        $step    = $request->get('step', '');
        $appName = $request->get('app_name', '');
        $version = (int) $request->get('version', 0);
        if (!in_array($step, ['deleteCode', 'deleteSql', 'uninstallData', 'success'])) {
            return $this->fail('卸载步骤错误');
        }
        if (empty($appName)) {
            return $this->fail('应用名称参数错误');
        }
        if (empty($version)) {
            return $this->fail('应用版本参数错误');
        }
        $class = new UninstallUtil($request, $appName, $version);
        return call_user_func([$class, $step]);
    }

    /**
     * 获取应用详情
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function detail(Request $request)
    {
        $appName = $request->get('app_name', '');
        return CloudService::getAppDetail($appName);
    }

    /**
     * 获取应用分类
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function category(Request $request)
    {
        return CloudService::getAppCategory();
    }

    /**
     * 获取应用类型
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function appType(Request $request)
    {
        return CloudService::getAppType();
    }

    /**
     * 获取应用安装类型
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function installStatus(Request $request)
    {
        return CloudService::getAppInstall();
    }
}
