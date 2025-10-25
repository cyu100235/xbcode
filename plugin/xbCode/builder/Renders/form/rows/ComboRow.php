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

use plugin\xbCode\builder\Components\Form\Combo;

/**
 * 组合组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait ComboRow
{
    /**
     * 组合多条组件
     * @param string $field
     * @param string $title
     * @param mixed $value
     * @param array $items
     * @param callable|array $option
     * @return Combo
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowCombo(string $field, string $title, mixed $value = '', array $items = [], callable|array $option= [])
    {
        // 排除重复的表单项
        $this->excludeFormRows($items);
        /** @var Combo */
        $component = $this->addRow(Combo::class, $field, $title, $value, $option);
        $component->items($items);
        return $component;
    }
}
