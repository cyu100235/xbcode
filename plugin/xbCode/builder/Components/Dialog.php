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
namespace plugin\xbCode\builder\Components;

/**
 * 对话框组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/dialog?page=1
 * @method $this title(string $value) 弹出层标题
 * @method $this body(string $value) 往 Dialog 内容区加内容
 * @method $this size(string $value) 指定 dialog 大小，支持: xs、sm、md、lg、xl、full
 * @method $this bodyClassName(string $value) Dialog body 区域的样式类名
 * @method $this closeOnEsc(bool $value) 是否支持按 Esc 关闭 Dialog
 * @method $this showCloseButton(bool $value) 是否显示右上角的关闭按钮
 * @method $this showErrorMsg(bool $value) 是否在弹框左下角显示报错信息
 * @method $this showLoading(bool $value) 是否在弹框左下角显示 loading 动画
 * @method $this disabled(bool $value) 如果设置此属性，则该 Dialog 只读没有提交操作。
 * @method $this actions(array $value) 【确认】和【取消】 如果想不显示底部按钮，可以配置：[]
 * @method $this data(array $value) 支持数据映射，如果不设定将默认将触发按钮的上下文中继承数据
 */
class Dialog extends BaseSchema
{
    public string $type = 'dialog';
}
