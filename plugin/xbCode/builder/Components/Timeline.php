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
 * 时间轴组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/timeline
 * @method $this className(string $value) 外层 Dom 的 CSS 类名
 * @method $this style(string $value) 外层 Dom 的内联样式
 * @method $this items(array $value) 时间轴节点数据
 * @method $this source(string $value) 时间轴节点数据源
 * @method $this mode(string $value) 指定文字相对于时间轴的位置，仅 direction=vertical 时支持
 * @method $this direction(string $value) 时间轴方向
 * @method $this reverse(bool $value) 根据时间倒序显示
 * @method $this iconClassName(string $value) 统一配置的节点图标 CSS 类
 * @method $this timeClassName(string $value) 统一配置的节点时间 CSS 类
 * @method $this titleClassName(string $value) 统一配置的节点标题 CSS 类
 * @method $this detailClassName(string $value) 统一配置的节点详情 CSS 类
 */
class Timeline extends BaseSchema
{
    public string $type = 'timeline';
}
