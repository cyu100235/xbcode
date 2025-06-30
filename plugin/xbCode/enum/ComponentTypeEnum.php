<?php
namespace plugin\xbCode\enum;

use plugin\xbCode\base\BaseEnum;

/**
 * 组件类型枚举
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class ComponentTypeEnum extends BaseEnum
{
    /**
     * 不使用组件
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    const NONE_TOB_INDEX = 'none/index';

    /**
     * 表单组件
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    const FORM_TOB_INDEX = 'form/index';

    /**
     * 普通表格
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    const TABLE_TOB_INDEX = 'table/index';

    /**
     * 侧边栏表格
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    const SIDEBAR_TOB_INDEX = 'table/sidebar';

    /**
     * 远程组件
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    const REMOTE_TOB_INDEX = 'remote/index';

    /**
     * 工作台
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    const WORKBENCH_TOB_INDEX = 'workbench/index';
}