<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCrontab\app\model;

use plugin\xbCode\Model;

/**
 * 定时任务日志
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class CrontabLog extends Model
{
    /**
     * 关联定时任务
     * @return \think\model\relation\HasOne
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function cron()
    {
        return $this->hasOne(Crontab::class, 'id', 'crontab_id');
    }
}
