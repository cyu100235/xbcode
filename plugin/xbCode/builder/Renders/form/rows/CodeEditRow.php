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

use plugin\xbCode\builder\Components\Form\Editor;

/**
 * 代码展示表单项
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait CodeEditRow
{
    /**
     * 添加代码编辑器组件
     * @param string $field
     * @param string $title
     * @param mixed $value
     * @param callable|array $option
     * @return Editor
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowCodeEdit(string $field, string $title, mixed $value = '', callable|array $option= [])
    {
        /** @var Editor */
        $component = $this->addRow(Editor::class, $field, $title, $value, $option);
        return $component;
    }
}
