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

use plugin\xbCode\builder\Components\AmisHtml;
use plugin\xbCode\builder\Components\Form\InputText;

/**
 * HTML表单项
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait HtmlRow
{
    /**
     * 添加HTML
     * @param string $html
     * @return AmisHtml
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowHtml(string $html)
    {
        /** @var AmisHtml */
        $component = $this->addRow(AmisHtml::class, '', '');
        $component->html($html);
        return $component;
    }
    /**
     * 添加表单HTML项
     * @param string $field 字段名
     * @param string $title 标题
     * @param string $html HTML内容，为空时渲染字段值
     * @param callable|array $option
     * @return InputText
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowFormHtml(string $field, string $title, string $html = '', callable|array $option= [])
    {
        /** @var InputText */
        $component = $this->addRow(InputText::class, $field, $title, '', $option);
        $component->static(true);
        $component->staticSchema([
            'type' => 'html',
            'html' => $html ? $html : "\${$field}",
        ]);
        return $component;
    }
}
