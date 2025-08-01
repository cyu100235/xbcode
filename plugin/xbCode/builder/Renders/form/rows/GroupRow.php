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
namespace plugin\xbCode\builder\Renders\form\rows;

use plugin\xbCode\builder\Components\Form\Group;

/**
 * 分组表单项
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait GroupRow
{
    /**
     * 添加表单项分组
     * @param array $components
     * @return Group
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowGroup(array $components)
    {
        // 排除重复的表单项
        $this->excludeFormRows($components);
        /** @var Group */
        $component = Group::make();
        $component->body($components);
        $this->formRows[] = $component;
        return $component;
    }
}