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
 * 日历组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/calendar
 * @method $this schedules(array $value) 设置日历中展示日程
 * @method $this scheduleClassNames(array $value) 设置日历中展示日程的颜色
 * @method $this scheduleAction(array $value) 自定义日程展示
 * @method $this largeMode(bool $value) 放大模式
 * @method $this todayActiveStyle(array $value) 今日激活时的自定义样式
 * @method $this className(string $value) 设置组件的类名
 * @method $this style(array $value) 设置组件的样式
 * @method $this id(string $value) 设置组件的唯一标识
 * @method $this visible(bool $value) 设置组件的可见性
 * @method $this visibleOn(string $value) 设置组件的可见性表达式
 * @method $this hidden(bool $value) 设置组件的隐藏性
 * @method $this hiddenOn(string $value) 设置组件的隐藏性表达式
 * @method $this disabled(bool $value) 设置组件的禁用状态
 */
class Calendar extends BaseSchema
{
    public string $type = 'calendar';
}
