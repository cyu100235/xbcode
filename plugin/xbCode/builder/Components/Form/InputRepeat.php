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
 * 重复频率选择器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link http://www.xbcode.net
 * @method $this options(string $value) 可选的重复频率secondly,minutely,hourly,daily,weekdays,weekly,monthly,yearly
 * @method $this placeholder(string $value) 当不指定值时的说明
 */
class InputRepeat extends FormBase
{
    public string $type = 'input-repeat';
}
