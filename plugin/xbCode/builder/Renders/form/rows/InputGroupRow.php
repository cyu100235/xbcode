<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Renders\form\rows;

use plugin\xbCode\builder\Components\Form\InputGroup;

/**
 * 输入框组表单项
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait InputGroupRow
{
    /**
     * 添加输入框组合
     * @param string $title 标题
     * @param array $components 组件列表
     * @param callable|array $option 组件参数
     * @return InputGroup
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function addRowInputGroup(string $title, array $components, callable|array $option= []): InputGroup
    {
        // 排除重复的表单项
        $this->excludeFormRows($components);
        /** @var InputGroup */
        $component = $this->addRow(InputGroup::class, '', $title, '', $option);
        $component->body($components);
        return $component;
    }
}
