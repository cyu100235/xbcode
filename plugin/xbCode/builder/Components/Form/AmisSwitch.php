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
 * 开关控件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/switch
 * @method $this option(string $value) 选项说明
 * @method $this onText(string $value) 开启时开关显示的内容
 * @method $this offText(string $value) 关闭时开关显示的内容
 * @method $this trueValue(string $value) 标识真值
 * @method $this falseValue(string $value) 标识假值
 * @method $this size(string $value) 开关大小
 * @method $this loading(bool $value) 是否处于加载状态
 * @method $this className(string $value) 外层 DOM 类名
 */
class AmisSwitch extends FormBase
{
    public string $type = 'switch';
}
