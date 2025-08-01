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
 * 时间组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-time
 * @method $this value(string $value) 默认值
 * @method $this valueFormat(string $value) 时间选择器值格式，更多格式类型请参考 moment
 * @method $this displayFormat(string $value) 时间选择器显示格式，即时间戳格式，更多格式类型请参考 moment
 * @method $this placeholder(string $value) 占位文本
 * @method $this clearable(bool $value) 是否可清除
 * @method $this timeConstraints(bool $value) 时间限制
 */
class InputTime extends FormOptions
{
    public string $type = 'input-time';
}
