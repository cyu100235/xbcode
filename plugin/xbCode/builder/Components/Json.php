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
 * JSON组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/json
 * @method $this className(string $value) 外层 CSS 类名
 * @method $this value(mixed $value) json 值，如果是 string 会自动 parse
 * @method $this placeholder(string $value) 占位文本
 * @method $this jsonTheme(string $value) 主题，可选twilight和eighties
 * @method $this ellipsisThreshold(int $value) 设置字符串的最大展示长度，点击字符串可以切换全量/部分展示方式，默认展示全量字符串
 * @method $this levelExpand(int $value) 默认展开的层级
 * @method $this source(string $value) 通过数据映射获取数据链中的值
 * @method $this mutable(bool $value) 是否可修改
 * @method $this displayDataTypes(bool $value) 是否显示数据类型
 * @method $this enableClipboard(bool $value) 是否启用复制按钮
 */
class Json extends BaseSchema
{
    public string $type = 'json';
}
