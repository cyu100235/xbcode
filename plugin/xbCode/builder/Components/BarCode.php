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
 * 条形码渲染器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/barcode
 * @method $this className(string $value) 外层 Dom 的类名
 * @method $this style(array $value) 外层 Dom 的样式
 * @method $this width(string $value) 条形码宽度
 * @method $this height(string $value) 条形码高度
 * @method $this options(array $value) 条形码配置项
 * @method $this value(string $value) 条形码值
 * @method $this name(string $value) 条形码名称
 */
class BarCode extends BaseSchema
{
    public string $type = 'barcode';
}
