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

use plugin\xbCode\builder\Components\Color;
use plugin\xbCode\builder\Components\Form\IconPicker;
use plugin\xbCode\builder\Components\Form\InputArray;
use plugin\xbCode\builder\Components\Form\InputSwitch;

/**
 * 表单项-点击型
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait FormRowClick
{
    /**
     * 添加开关组件
     * @param string $field
     * @param string $title
     * @param string $value
     * @param callable|array $option
     * @return InputSwitch
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowSwitch(string $field, string $title, string $value = '', callable|array $option = [])
    {
        /** @var InputSwitch */
        $component = $this->addRow(InputSwitch::class, $field, $title, $value, $option);
        return $component;
    }

    /**
     * 添加图标选择器
     * @param string $field
     * @param string $title
     * @param string $value
     * @param callable|array $option
     * @return IconPicker
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowIconPicker(string $field, string $title, string $value = '', callable|array $option = [])
    {
        /** @var IconPicker */
        $component = $this->addRow(IconPicker::class, $field, $title, $value, $option);
        return $component;
    }

    /**
     * 添加颜色选择器
     * @param string $field
     * @param string $title
     * @param string $value
     * @param callable|array $option
     * @return Color
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowColor(string $field, string $title, string $value = '', callable|array $option = [])
    {
        /** @var Color */
        $component = $this->addRow(Color::class, $field, $title, $value, $option);
        return $component;
    }

    /**
     * 添加数组输入组件
     * @param string $field
     * @param string $title
     * @param string $value
     * @param callable|array $option
     * @return InputArray
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowArray(string $field, string $title, string $value = '', callable|array $option = [])
    {
        /** @var InputArray */
        $component = $this->addRow(InputArray::class, $field, $title, $value, $option);
        return $component;
    }
}
