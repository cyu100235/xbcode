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
namespace plugin\xbCode\builder\Components\Action;

use plugin\xbCode\builder\Components\Button;
use plugin\xbCode\builder\Components\Service;

/**
 * 弹窗行为
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/action
 * @method $this id(string $value) 按钮ID
 * @method $this icon(string $value) 按钮图标
 * @method $this label(string $value) 按钮文本
 * @method $this className(string $value) 外层 Dom 的类名
 * @method $this url(string $value) 点击跳转的地址，指定此属性 button 的行为和 a 链接一致
 * @method $this actionType(string $value) 设置按钮类型
 * @method $this level(string $value) 按钮样式
 * - `link` 链接样式
 * - `primary` 主要按钮样式
 * - `enhance` 增强按钮样式
 * - `secondary` 次要按钮样式
 * - `info` 信息按钮样式
 * - `success` 成功按钮样式
 * - `warning` 警告按钮样式
 * - `danger` 危险按钮样式
 * - `light` 浅色按钮样式
 * - `dark` 深色按钮样式
 * - `default` 默认按钮样式
 * @method $this tooltip(string $value) 气泡提示内容
 * @method $this tooltipPlacement(string $value) 气泡框位置器
 * @method $this tooltipTrigger(string $value) 触发 tooltip
 * @method $this disabled(bool $value) 按钮失效状态
 * @method $this disabledTip(string $value) 按钮失效提示
 * @method $this block(bool $value) 将按钮宽度调整为其父宽度的选项
 * @method $this loading(bool $value) 显示按钮 loading 效果
 * @method $this loadingOn(string $value) 显示按钮 loading 表达式
 * @method $this confirmText(string $value) 确认按钮文本
 * @method $this confirmLevel(string $value) 确认按钮样式
 * @method $this confirmType(string $value) 确认按钮类型
 * @method $this nextCondition(mixed $value) 可以用来设置下一条数据的条件，默认为 true
 * @method $this reload(mixed $value) 是否刷新当前页面
 * @method $this redirect(mixed $value) 是否跳转到指定页面
 * @method $this dialog(mixed $value) 弹窗配置
 * - `title` string 弹出层标题
 * - `body` mixed 往弹窗内容区加内容
 * - `size` string 指定弹窗大小，支持: xs、sm、md、lg、xl、full、custom
 * - `width` number 弹窗宽度, size 为 custom 时生效
 * - `height` number 弹窗高度,size为custom时生效
 * - `bodyClassName` 弹窗的body区域的样式类名
 * - `closeOnEsc` boolean 是否支持按Esc关闭弹窗
 * - `showCloseButton` boolean 是否显示右上角的关闭按钮
 * - `showErrorMsg` boolean 是否在弹框左下角显示报错信息
 * - `showLoading` boolean 是否在弹框左下角显示loading动画
 * - `disabled` boolean 如果设置此属性，则该弹窗只读没有提交操作。
 * - `draggable` boolean 是否支持拖拽弹窗
 * - `actions` <Action> 【确认】和【取消】如果想不显示底部按钮，可以配置：[]
 * - `data` array 支持数据映射，如果不设定将默认将触发按钮的上下文中继承数据。
 */
class DialogAction extends Button
{
    public string $actionType = 'dialog';

    /**
     * 模态框弹窗
     * @param string $url 视图URL
     * @param string $label 按钮文本
     * @param array $option 弹窗配置
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function dialogModel(string $url, string $label, array $option = [])
    {
        $this->level('primary');
        $this->label($label);
        $dialog = array_merge([
            'title' => '未设置弹窗标题',
            'size' => 'md',
        ], $option, [
            'body' => Service::make()->schemaApi($url)
        ]);
        $this->dialog($dialog);
        return $this;
    }

    /**
     * 设置弹窗标题，支持模板语法
     * @param string $title 标题
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function title(string $title)
    {
        $this->dialog['title'] = $title;
        return $this;
    }

    /**
     * 设置弹窗尺寸
     * @param string $size 指定弹窗大小
     * - `xs` - 超小尺寸
     * - `sm` - 小尺寸
     * - `md` - 中等尺寸（默认）
     * - `lg` - 大尺寸
     * - `xl` - 超大尺寸
     * - `full` - 全屏尺寸
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function size(string $size)
    {
        $this->dialog['size'] = $size;
        return $this;
    }

    /**
     * 设置弹窗按钮
     * @param array $actions 按钮数组
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function actions(array $actions)
    {
        $this->dialog['actions'] = $actions;
        return $this;
    }

    /**
     * 取消弹窗按钮
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function cancelActions()
    {
        $this->actions([]);
        return $this;
    }

    /**
     * 是否支持拖拽弹窗
     * @param bool $value 是否支持拖拽弹窗
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function draggable(bool $value)
    {
        $this->dialog['draggable'] = $value;
        return $this;
    }

    /**
     * 是否支持按Esc关闭弹窗
     * @param bool $value
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function closeOnEsc(bool $value)
    {
        $this->dialog['closeOnEsc'] = $value;
        return $this;
    }

    /**
     * 是否显示右上角的关闭按钮
     * @param bool $value
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function showCloseButton(bool $value)
    {
        $this->dialog['showCloseButton'] = $value;
        return $this;
    }

    /**
     * 是否在弹框左下角显示报错信息
     * @param bool $value
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function showErrorMsg(bool $value)
    {
        $this->dialog['showErrorMsg'] = $value;
        return $this;
    }

    /**
     * 是否在弹框左下角显示loading动画
     * @param bool $value
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function showLoading(bool $value)
    {
        $this->dialog['showLoading'] = $value;
        return $this;
    }
}
