<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   积木云
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */

namespace plugin\xbUploadCos\app\controller;

use support\Response;
use plugin\xbCode\api\PluginJson;

/**
 * 插件默认控制器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class IndexController extends BaseController
{
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
}
