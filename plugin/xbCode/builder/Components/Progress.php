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
 * 进度条组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/progress
 * @method $this mode(string $value) 设置进度条类型，可选line circle dashboard
 * @method $this className(string $value) 设置外层 CSS 类名
 * @method $this value(string $value) 设置进度值
 * @method $this placeholder(string $value) 设置占位文本
 * @method $this showLabel(bool $value) 是否展示进度文本
 * @method $this stripe(bool $value) 背景是否显示条纹
 * @method $this animate(bool $value) type 为 line，可支持动画
 * @method $this map(string|array $value) 进度颜色映射
 * @method $this threshold(string|array $value) 阈值（刻度）
 * @method $this showThresholdText(bool $value) 是否显示阈值（刻度）数值
 * @method $this valueTpl(string $value) 自定义格式化内容
 * @method $this strokeWidth(int $value) 进度条线宽度
 * @method $this gapDegree(int $value) 仪表盘缺角角度，可取值 0 ~ 295
 * @method $this gapPosition(string $value) 仪表盘进度条缺口位置，可选top bottom left right
 */
class Progress extends BaseSchema
{
    public string $type = 'progress';
}
