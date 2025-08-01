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

use plugin\xbCode\builder\Components\Custom\XbWangEditor;

/**
 * 链接输入框表单项
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait WangEditorRow
{
    /**
     * 添加王德发编辑器
     * @param string $field
     * @param string $title
     * @param mixed $value
     * @param callable|array $option
     * @return XbWangEditor
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowEditorWang(string $field, string $title, mixed $value = '', callable|array $option= [])
    {
        /** @var XbWangEditor */
        $component = $this->addRow(XbWangEditor::class, $field, $title, $value, $option);
        $component->options([
            'height' => 500,
            'defaultConfig' => [
                'placeholder' => '请输入内容',
            ],
        ]);
        return $component;
    }
}
