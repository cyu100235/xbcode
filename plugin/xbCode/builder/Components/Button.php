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
 * Button 按钮渲染器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/button
 * @method $this id(string $value) 按钮ID
 * @method $this label(string $value) 按钮文本
 * @method $this icon(string $value) 按钮图标
 * @method $this className(string $value) 外层 Dom 的类名
 * @method $this url(string $value) 点击跳转的地址，指定此属性行为和 a 链接一致
 * @method $this size(string $value) 设置按钮大小
 * @method $this actionType(string $value) 设置按钮类型
 * @method $this level(string $value) 设置按钮样式
 * @method $this tooltip(string $value) 气泡提示内容
 * @method $this tooltipPlacement(string $value) 气泡框位置器
 * @method $this tooltipTrigger(string $value) 触发 tooltip
 * @method $this disabled(bool $value) 按钮失效状态
 * @method $this block(bool $value) 将按钮宽度调整为其父宽度的选项
 * @method $this loading(bool $value) 显示按钮 loading 效果
 * @method $this loadingOn(string $value) 显示按钮 loading 表达式
 * @method $this confirmText(string $value) 确认框消息文本
 * @method $this confirmLevel(string $value) 确认按钮样式
 * @method $this confirmType(string $value) 确认按钮类型
 */
class Button extends BaseSchema
{
    /**
     * 按钮类型
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public string $type = 'button';

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
            $this->className('text-dark');
        } else {
            $this->level('dark');
        }
        return $this;
    }
}
