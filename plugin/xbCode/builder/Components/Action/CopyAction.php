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
namespace plugin\xbCode\builder\Components\Action;

use plugin\xbCode\builder\Components\Button;

/**
 * 复制行为
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/action
 * @method $this actionType(string $value) 复制一段内容到粘贴板
 * @method $this content(string $value) 指定复制的内容。可用 ${xxx} 取值
 */
class CopyAction extends Button
{
    public string $actionType = 'copy';
}
