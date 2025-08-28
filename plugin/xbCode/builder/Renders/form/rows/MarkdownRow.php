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

use plugin\xbCode\builder\Components\Markdown;
use plugin\xbCode\builder\Components\Form\Editor;

/**
 * Markdown表单项
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait MarkdownRow
{
    /**
     * 添加Markdown编辑器
     * @param string $field
     * @param string $title
     * @param mixed $value
     * @param callable|array $option
     * @return \plugin\xbCode\builder\Components\Form\Group
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowEditorMarkdown(string $field, string $title, mixed $value = '', callable|array $option= [])
    {
        $component = $this->addRowGroup([
            $this->addRow(Editor::class, $field, $title, $value, $option)->options([
                'height' => 500,
                'editor' => [
                    'theme' => 'vs-dark',
                ],
                'theme' => 'vs-dark',
            ])->language('markdown'),
            $this->addRow(Markdown::class, $field, '', $value, $option),
        ]);
        return $component;
    }
}
