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
 * 复选框组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/checkbox
 * @method $this option(string $value) 选项说明
 * @method $this trueValue(string $value) 标识真值
 * @method $this falseValue(string $value) 标识假值
 * @method $this optionType(string $value) 类型 default｜button
 */
class Checkbox extends FormBase
{
    public string $type = 'checkbox';

}
