<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @version  1.0
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCrontab\app\events;

use plugin\xbCrontab\app\model\CrontabLog;

/**
 * 定时任务日志事件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class CrontabLogEvent
{
    /**
     * 删除定时任务日志
     * @param array $crontab
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function delete(array $crontab)
    {
        CrontabLog::where('crontab_id', $crontab['id'])->delete();
    }
}
