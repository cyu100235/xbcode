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
 * 步骤条组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/steps
 * @method $this className(string $value) 外层 Dom 的 CSS 类名
 * @method $this steps(array $value) 步骤条配置
 * @method $this source(string $value) 数据源
 * @method $this value(string $value) 选中值
 * @method $this name(string $value) 关联上下文变量
 * @method $this mode(string $value) 模式 horizontal | 竖直vertical | 简单simple
 * @method $this labelPlacement(string $value) 标签位置 horizontal | vertical	horizontal
 * @method $this progressDot(string $value) 点状步骤条
 */
class Steps extends BaseSchema
{
    public string $type = 'steps';
}
