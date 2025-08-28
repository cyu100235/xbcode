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

use plugin\xbCode\builder\Components\Form\InputRichText;

/**
 * 普通编辑器表单项
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait NormalEditorRow
{
    /**
     * 添加Tinymce编辑器
     * @param string $field
     * @param string $title
     * @param mixed $value
     * @param callable|array $option
     * @return InputRichText
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowEditorTinymce(string $field, string $title, mixed $value = '', callable|array $option = [])
    {
        /** @var InputRichText */
        $component = $this->addRow(InputRichText::class, $field, $title, $value, $option);
        $component->options([
            'height' => 500,
            'menubar' => false,
            'plugins' => 'autosize advlist autolink lists link image media charmap anchor print fullscreen code table insertdatetime wordcount preview',
        ]);
        $component->placeholder('请输入内容');
        return $component;
    }

    /**
     * 添加Froala编辑器
     * @param string $field
     * @param string $title
     * @param mixed $value
     * @param callable|array $option
     * @return InputRichText
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowEditorFroala(string $field, string $title, mixed $value = '', callable|array $option = [])
    {
        /** @var InputRichText */
        $component = $this->addRow(InputRichText::class, $field, $title, $value, $option);
        $component->vendor('froala');
        $component->options([
            'height' => 500,
        ]);
        $component->placeholder('请输入内容');
        return $component;
    }
}
