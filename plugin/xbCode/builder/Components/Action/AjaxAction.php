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
namespace plugin\xbCode\builder\Components\Action;

use plugin\xbCode\builder\Components\Button;

/**
 * 网络请求行为
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/action
 * @method $this api(string|array $value) 接口地址
 * @method $this icon(string $value) 按钮图标
 * @method $this block(bool $value) 将按钮宽度调整为其父宽度的选项
 * @method $this feedback(array $value) 当 ajax 返回正常后，还能接着弹出一个 dialog 做其他交互。返回的数据可用于这个 dialog
 * @method $this reload(string $value) 刷新页面
 * @method $this redirect(string $value) 重定向地址
 * @method $this messages(array $value) 操作成功提示信息(可选)
 * @method $this confirmText(string $value) 确认框提示信息(可选)
 * @method $this confirmTitle(string $value) 确认框标题(可选)
 * @method $this level(string $value) 按钮样式
 * - `link` - 链接样式
 * - `primary` - 主要按钮样式
 * - `enhance` - 增强按钮样式
 * - `secondary` - 次要按钮样式
 * - `info` - 信息按钮样式
 * - `success` - 成功按钮样式
 * - `warning` - 警告按钮样式
 * - `danger` - 危险按钮样式
 * - `light` - 浅色按钮样式
 * - `dark` - 深色按钮样式
 * - `default` - 默认按钮样式
 */
class AjaxAction extends Button
{
    public string $actionType = 'ajax';
}
