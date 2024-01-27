<?php
namespace app\admin\controller;

use app\common\BaseController;
use app\common\service\CloudService;

/**
 * 系统更新服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class UpdateController extends BaseController
{
    /**
     * 获取框架升级信息
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function getFrameUpdate()
    {
        return CloudService::getFrameUpdate();
    }

    /**
     * 框架升级处理
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function updateFrame()
    {
        return CloudService::downloadFrame();
    }

    /**
     * 获取框架日志
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function getLogList()
    {
        return CloudService::getFrameLogList();
    }
}
