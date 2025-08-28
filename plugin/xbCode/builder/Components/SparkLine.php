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
 * 走势图组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/sparkline
 * @method $this className(string $value) 外层 CSS 类名
 * @method $this name(string $value) 关联的变量
 * @method $this width(string $value) 宽度
 * @method $this height(string $value) 高度
 * @method $this placeholder(string $value) 数据为空时显示的内容
 * @method $this clickAction(string $value) 点击时的操作
 * @method $this value(string $value) 数据
 */
class SparkLine extends BaseSchema
{
    public string $type = 'sparkline';
}
