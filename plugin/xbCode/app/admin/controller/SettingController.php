<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @version  1.0
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\app\admin\controller;

use support\Request;
use plugin\xbCode\XbController;
use plugin\xbCode\trait\ConfigTrait;

/**
 * 系统配置控制器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class SettingController extends XbController
{
    use ConfigTrait;

    /**
     * 配置项
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function system(Request $request)
    {
        return $this->normalConfig();
    }

    /**
     * 版权信息
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function webicp(Request $request)
    {
        return $this->normalConfig();
    }
}