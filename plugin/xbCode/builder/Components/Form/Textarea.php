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
 * 文本域组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/textarea
 * @method $this minRows(string $value) 最小行数
 * @method $this maxRows(string $value) 最大行数
 * @method $this trimContents(bool $value) 是否去除首尾空白文本
 * @method $this readOnly(bool $value) 是否只读
 * @method $this showCounter(bool $value) 是否显示计数器
 * @method $this maxLength(int $value) 限制最大字数
 * @method $this clearable(bool $value) 是否可清除
 * @method $this resetValue(string $value) 清除后设置此配置项给定的值
 */
class Textarea extends FormBase
{
    public string $type = 'textarea';
}
