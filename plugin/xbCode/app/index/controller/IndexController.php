<?php
/**
 * 积木云渲染器
 * @package  XbCode.0
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\app\index\controller;

use plugin\xbCode\XbController;
use plugin\xbCode\api\AppEntry;
use plugin\xbCode\api\ConfigApi;

/**
 * 首页控制器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class IndexController extends XbController
{
    /**
     * 首页
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function index()
    {
        return $this->viewAppView($this->webIcpVars());
    }

    /**
     * 组织及备案信息变量
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function webIcpVars(): array
    {
        $system = ConfigApi::make('system')->get('system', []);
        $webIcp = AppEntry::replaceVariables(ConfigApi::make('webicp')->get('', []), $system);
        return [
            'webIcp' => $webIcp,
            'webName' => $system['web_name'] ?? '',
        ];
    }
}
