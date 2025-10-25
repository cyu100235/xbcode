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
namespace plugin\xbCode\builder\Components\Custom;

use plugin\xbCode\builder\Components\BaseSchema;

/**
 * Element-Plus提示组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link http://www.xbcode.net
 * @method $this title(string $value) 标题
 * @method $this type(string $value) 类型 'primary' | 'success' | 'warning' | 'info' | 'error'
 * @method $this content(string $value) 内容（支持HTML）
 * @method $this closable(bool $value) 是否可关闭
 * @method $this center(bool $value) 是否居中
 * @method $this closeText(string $value) 关闭按钮文本
 * @method $this showIcon(bool $value) 是否显示类型图标
 * @method $this effect(string $value) 主题样式 'light' | 'dark'
 * @method $this showAfter (int $value) 在触发后多久显示内容，单位：毫秒
 * @method $this hideAfter  (int $value) 延迟关闭，单位：毫秒
 * @method $this autoClose  (int $value) 出现后自动隐藏延时，单位：毫秒
 */
class Alert extends BaseSchema
{
}
