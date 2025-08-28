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
 * 静态数据展示
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/static
 * @method $this tpl(string $value) 模板
 * @method $this text(string $value) 文本
 * @method $this popOver(string $value) 弹层
 * @method $this quickEdit(array $value) 快速编辑
 * @method $this copyable(string $value) 可复制
 * @method $this borderMode(string $value) 边框模式
 * @method $this value(string $value) 展示数据值
 */
class StaticExact extends FormBase
{
    public string $type = 'static';
}
