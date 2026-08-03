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
namespace plugin\xbCode\builder\Components;

/**
 * 下拉按钮组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/dropdown-button
 * @method $this label(string $value) 设置按钮文本
 * @method $this className(string $value) 设置外层 CSS 类名
 * @method $this btnClassName(string $value) 设置按钮 CSS 类名
 * @method $this menuClassName(string $value) 设置下拉菜单 CSS 类名
 * @method $this block(bool $value) 设置块状样式
 * @method $this size(string $value) 设置尺寸，支持'xs'、'sm'、'md' 、'lg'
 * @method $this align(string $value) 设置位置，可选'left'或'right'
 * @method $this buttons(array $value) 设置下拉按钮
 * @method $this iconOnly(bool $value) 设置只显示 icon
 * @method $this defaultIsOpened(bool $value) 设置默认是否打开
 * @method $this closeOnOutside(bool $value) 设置点击外侧区域是否收起
 * @method $this closeOnClick(bool $value) 设置点击按钮后自动关闭下拉菜单
 * @method $this trigger(string $value) 设置触发方式
 * @method $this hideCaret(bool $value) 设置隐藏下拉图标
 */
class DropdownButton extends BaseSchema
{
    public string $type = 'dropdown-button';
    public bool $closeOnClick = true;

    /**
     * 主按钮
     * @param bool $text 是否显示文本
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function primary(bool $text = false)
    {
        if ($text) {
            $this->className('text-primary');
        } else {
            $this->level('primary');
        }
        return $this;
    }

    /**
     * 次按钮
     * @param bool $text 是否显示文本
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function secondary(bool $text = false)
    {
        if ($text) {
            $this->className('text-secondary');
        } else {
            $this->level('secondary');
        }
        return $this;
    }

    /**
     * 成功按钮
     * @param bool $text 是否显示文本
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function success(bool $text = false)
    {
        if ($text) {
            $this->className('text-success');
        } else {
            $this->level('success');
        }
        return $this;
    }

    /**
     * 警告按钮
     * @param bool $text 是否显示文本
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function warning(bool $text = false)
    {
        if ($text) {
            $this->className('text-warning');
        } else {
            $this->level('warning');
        }
        return $this;
    }

    /**
     * 危险按钮
     * @param bool $text 是否显示文本
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function danger(bool $text = false)
    {
        if ($text) {
            $this->className('text-danger');
        } else {
            $this->level('danger');
        }
        return $this;
    }

    /**
     * 亮按钮
     * @param bool $text 是否显示文本
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function light(bool $text = false)
    {
        if ($text) {
            $this->className('text-light');
        } else {
            $this->level('light');
        }
        return $this;
    }

    /**
     * 暗按钮
     * @param bool $text 是否显示文本
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function dark(bool $text = false)
    {
        if ($text) {
            $this->className('text-default');
        } else {
            $this->level('dark');
        }
        return $this;
    }
}
