<?php
namespace plugin\{PLUGIN_NAME}\app\controller;

use app\common\XbController;
use support\Request;

/**
 * 首页控制器
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class IndexController extends XbController
{
    public function index(Request $request)
    {
        return view('index/index', ['name' => '{PLUGIN_NAME}']);
    }
}