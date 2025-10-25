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
 * 操作选项组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link http://www.xbcode.net
 * @method $this placeholder(string $value) 占位符
 * @method $this className(string $value) 外层 Dom 的类名
 * @method $this buttons(array $value) 操作按钮集合
 */
class Operation extends BaseSchema
{
    public string $type = 'operation';
}
