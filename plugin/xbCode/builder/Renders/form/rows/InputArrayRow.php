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

use plugin\xbCode\builder\Components\Form\InputArray;

/**
 * 数组表单项
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait InputArrayRow
{
    /**
     * 添加数组框
     * @param string $field
     * @param string $title
     * @param mixed $value
     * @param callable|array $option
     * @return InputArray
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowInputArray(string $field, string $title, mixed $value = '', array $items = [], callable|array $option= [])
    {
        // 排除重复的表单项
        $this->excludeFormRows($items);
        /** @var InputArray */
        $component = $this->addRow(InputArray::class, $field, $title, $value, $option);
        $component->items($items);
        return $component;
    }
}
