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
 * 循环渲染器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/each
 * @method $this value(array $value) 循环的值
 * @method $this name(string $value) 获取数据域中变量
 * @method $this source(string $value) 获取数据域中变量， 支持 数据映射
 * @method $this items(mixed $value) 使用value中的数据，循环输出渲染器。
 * @method $this placeholder(string $value) 当 value 值不存在或为空数组时的占位文本
 * @method $this itemKeyName(string $value) 获取循环当前数组成员
 * @method $this indexKeyName(string $value) 获取循环当前索引
 */
class Each extends BaseSchema
{
    public string $type = 'each';
}
