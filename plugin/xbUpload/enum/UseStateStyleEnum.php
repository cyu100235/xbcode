<?php
namespace plugin\xbUpload\enum;

use plugin\xbCode\base\BaseEnum;

/**
 * 使用状态样式枚举
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class UseStateStyleEnum extends BaseEnum
{
    /**
     * 未使用
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    const STATE_TO_10 = 'danger';

    /**
     * 使用中
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    const STATE_TO_20 = 'success';
}