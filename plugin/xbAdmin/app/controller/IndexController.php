<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   dddd
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */

namespace plugin\xbAdmin\app\controller;

use support\Response;
use plugin\xbCode\api\PluginJson;
use plugin\xbAdmin\trait\XbViewsTrait;

/**
 * 插件默认控制器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class IndexController extends BaseController
{
    use XbViewsTrait;

    /**
     * 插件首页
     * @return Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function index()
    {
        // 读取插件自身 plugin.json（含注入的 name 及 title/desc），作为视图数据传入
        $path = str_replace('\\', '/', dirname(__DIR__, 2)) . '/plugin.json';
        return $this->viewAppView(PluginJson::get(null, null, $path));
    }

    /**
     * 后台单页应用入口
     *
     * 与 admin 模块的 Index::admin 指向同一份产物，
     * 便于在未启用模块路由时仍可直接访问 /xbAdmin/index/admin。
     * @return Response
     */
    public function admin()
    {
        return $this->adminView();
    }
}
