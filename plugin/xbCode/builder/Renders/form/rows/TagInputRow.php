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

use plugin\xbCode\builder\Components\Form\InputText;

/**
 * 标签框表单项
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait TagInputRow
{
    /**
     * 添加标签框
     * @param string $field
     * @param string $title
     * @param mixed $value
     * @param callable|array $option
     * @return InputText
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowInputTag(string $field, string $title, mixed $value = '', callable|array $option= [])
    {
        /** @var InputText */
        $component = $this->addRow(InputText::class, $field, $title, $value, $option);
        $component->type('input-tag');
        return $component;
    }
}
