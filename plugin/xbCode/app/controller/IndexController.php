<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\app\controller;

use plugin\xbCode\api\PluginJson;
use plugin\xbCode\app\XbController;

/**
 * 默认控制器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class IndexController extends XbController
{
    /**
     * 默认首页
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function index()
    {
        $path = str_replace('\\', '/', dirname(__DIR__, 2)). '/plugin.json';
        $plugin = PluginJson::get(null, null, $path);
        return $this->viewAppView($plugin);
    }
}
