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

use plugin\xbCode\builder\Components\Flex;

/**
 * Flex布局表单项
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait FlexRow
{
    /**
     * 添加Flex布局组件
     * @param array $components
     * @return Flex
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowFlex(array $components)
    {
        // 排除重复的表单项
        $this->excludeFormRows($components);
        /** @var Flex */
        $component = Flex::make();
        $component->items($components);
        $this->formRows[] = $component;
        return $component;
    }
}