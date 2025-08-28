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
 * Breadcrumb 面包屑渲染器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/breadcrumb
 * @method $this itemClassName(string $value) 导航项类名
 * @method $this separator(string $value) 分隔符
 * @method $this separatorClassName(string $value) 分隔符类名
 * @method $this dropdownClassName(string $value) 下拉菜单类名
 * @method $this dropdownItemClassName(string $value) 下拉菜单项类名
 * @method $this items(mixed $value) 下拉菜单项
 * @method $this labelMaxLength(int $value) 最大展示长度
 * @method $this tooltipPosition(string $value) 浮窗提示位置
 * @method $this source(string $value) 动态数据
 */
class Breadcrumb extends BaseSchema
{
    public string $type = 'breadcrumb';
}
