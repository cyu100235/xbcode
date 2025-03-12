<?php
namespace plugin\xbCode\enum;

use plugin\xbCode\base\BaseEnum;

/**
 * 开关枚举
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class ComponentEnum extends BaseEnum
{
    /**
     * 不使用组件
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    const NONE_TOB_INDEX = '不使用组件';

    /**
     * 表单组件
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    const FORM_TOB_INDEX = '表单组件';

    /**
     * 普通表格
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    const TABLE_TOB_INDEX = '普通表格';

    /**
     * 侧边栏表格
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    const SIDEBAR_TOB_INDEX = '侧边栏表格';

    /**
     * 远程组件
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    const REMOTE_TOB_INDEX = '远程组件';

    /**
     * 工作台
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    const WORKBENCH_TOB_INDEX = '工作台组件';
}