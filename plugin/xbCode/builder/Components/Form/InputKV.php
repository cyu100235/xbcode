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
 * 输入键值对
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-kv
 * @method $this valueType(string $value) 设置值类型
 * @method $this keyPlaceholder(string $value) 设置 key 的提示信息
 * @method $this valuePlaceholder(string $value) 设置 value 的提示信息
 * @method $this defaultValue(string $value) 设置默认值
 * @method $this draggable(bool $value) 设置是否可拖拽排序
 * @method $this autoParseJSON(bool $value) 设置是否自动转换 JSON 对象字符串
 */
class InputKV extends FormBase
{
    public string $type = 'input-kv';
}
