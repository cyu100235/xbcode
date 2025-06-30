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
 * 二维码组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/qrcode
 * @method $this className(string $value) 外层 CSS 类名
 * @method $this qrcodeClassName(string $value) 二维码的 CSS 类名
 * @method $this codeSize(int $value) 二维码的宽高大小
 * @method $this backgroundColor(string $value) 设置二维码背景色
 * @method $this foregroundColor(string $value) 设置二维码前景色
 * @method $this level(string $value) 二维码复杂级别，有（'L' 'M' 'Q' 'H'）四种
 * @method $this value(string $value) 扫描二维码后显示的文本，如果要显示某个页面请输入完整 url，支持使用 模板
 * @method $this imageSettings(array $value) 二维码图片配置
 */
class QRCode extends BaseSchema
{
    public string $type = 'qrcode';
}
