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
namespace plugin\xbCode\builder\Renders\Form\rows;

use plugin\xbCode\builder\Components\Form\Editor;
use plugin\xbCode\builder\Components\Form\InputImage;

/**
 * 表单项-展示型
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait FormRowDisplay
{
    /**
     * 添加图片组件
     * @param string $field
     * @param string $title
     * @param string $value
     * @param callable|array $option
     * @return InputImage
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowImage(string $field, string $title, string $value = '', callable|array $option = []): InputImage
    {
        /** @var InputImage */
        $component = $this->addRow(InputImage::class, $field, $title, $value, $option);
        return $component;
    }

    /**
     * 添加代码编辑器组件
     * @param string $field
     * @param string $title
     * @param string $value
     * @param callable|array $option
     * @return Editor
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowCodeEdit(string $field, string $title, string $value = '', callable|array $option = []): Editor
    {
        /** @var Editor */
        $component = $this->addRow(Editor::class, $field, $title, $value, $option);
        return $component;
    }
}
