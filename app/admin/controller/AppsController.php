<?php

namespace app\admin\controller;

use app\common\BaseController;
use app\common\service\CloudService;
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
        $page = (int)$request->get('page',1);
        $limit = (int)$request->get('limit',20);
        $keyword = $request->get('keyword','');
        $category = $request->get('category','');
        $platform = $request->get('platform','');
        $install = $request->get('install','');
        $params = [
            'page'      => $page,
            'limit'     => $limit,
            'keyword'   => $keyword,
            'category'  => $category,
            'platform'  => $platform,
            'install'   => $install,
        ];
        return CloudService::getAppList($params);
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
        $appName = $request->get('app_name','');
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
