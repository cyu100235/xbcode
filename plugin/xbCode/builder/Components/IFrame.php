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
 * IFrame 组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/iframe
 * @method $this className(string $value) 设置外层 CSS 类名
 * @method $this frameBorder(array $value) frameBorder
 * @method $this style(array $value) 样式对象
 * @method $this src(string $value) iframe 地址
 * @method $this allow(string $value) allow 配置
 * @method $this sandbox(string $value) sandbox 配置
 * @method $this referrerpolicy(string $value) referrerpolicy 配置
 * @method $this height(string|int $value) iframe 高度
 * @method $this width(string|int $value) iframe 宽度
 */
class IFrame extends BaseSchema
{
    public string $type = 'iframe';
}
