<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCrontab\enum;

use plugin\xbCode\base\BaseEnum;

/**
 * Cron预设周期枚举
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class CronPresetsEnum extends BaseEnum
{
    const STATE1 = [
        'label' => '每5分钟',
        'value' => '*/5 * * * *',
    ];
    const STATE2 = [
        'label' => '每10分钟',
        'value' => '*/10 * * * *',
    ];
    const STATE3 = [
        'label' => '每15分钟',
        'value' => '*/15 * * * *',
    ];
    const STATE4 = [
        'label' => '每30分钟',
        'value' => '*/30 * * * *',
    ];
    const STATE5 = [
        'label' => '每小时整点',
        'value' => '0 * * * *',
    ];
    const STATE6 = [
        'label' => '每天凌晨2点',
        'value' => '0 2 * * *',
    ];
    const STATE7 = [
        'label' => '每天凌晨3点',
        'value' => '0 3 * * *',
    ];
    const STATE8 = [
        'label' => '每天午夜',
        'value' => '0 0 * * *',
    ];
    const STATE9 = [
        'label' => '每天上午9点',
        'value' => '0 9 * * *',
    ];
    const STATE10 = [
        'label' => '每天中午12点',
        'value' => '0 12 * * *',
    ];
    const STATE11 = [
        'label' => '每天下午6点',
        'value' => '0 18 * * *',
    ];
    const STATE12 = [
        'label' => '每周一凌晨',
        'value' => '0 0 * * 1',
    ];
    const STATE13 = [
        'label' => '每周日凌晨',
        'value' => '0 0 * * 0',
    ];
    const STATE14 = [
        'label' => '每月1日凌晨',
        'value' => '0 0 1 * *',
    ];
    const STATE15 = [
        'label' => '每月15日凌晨',
        'value' => '0 0 15 * *',
    ];
    const STATE16 = [
        'label' => '自定义表达式',
        'value' => 'manual',
    ];

    /**
     * 获取选项值映射
     * @return array
     */
    public static function valueMap(): array
    {
        $data = static::toArray();
        $map = [];
        foreach ($data as $item) {
            $map[$item['value']] = $item['label'];
        }
        return $map;
    }
}
