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
namespace plugin\xbCode\builder\Components\Form;

/**
 * 图表单选框组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/chart-radios
 * @method $this config(array $value) 图表配置
 * @method $this showTooltipOnHighlight(bool $value) 高亮时是否显示 tooltip
 * @method $this chartValueField(string $value) 图表数值字段名
 */
class ChartRadios extends FormOptions
{
    public string $type = 'chart-radios';
}
