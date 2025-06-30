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
 * Markdown组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/markdown
 * @method $this name(string $value) 名称
 * @method $this value(string $value) 静态值
 * @method $this className(string $value) 类名
 * @method $this src(string $value) 外部地址
 */
class Markdown extends BaseSchema
{
    public string $type = 'markdown';
}
