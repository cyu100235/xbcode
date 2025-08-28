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
 * 加载中组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/spinner
 * @method $this show(bool $value) 是否显示 spinner 组件
 * @method $this className(string $value) spinner 图标父级标签的自定义 class
 * @method $this spinnerClassName(string $value) 组件中 icon 所在标签的自定义 class
 * @method $this spinnerWrapClassName(string $value) 作为容器使用时组件最外层标签的自定义 class
 * @method $this mode(string $value) 组件模式
 * @method $this size(string $value) 组件大小 sm lg
 * @method $this icon(string $value) 组件图标，可以是amis内置图标，也可以是字体图标或者网络图片链接，作为 ui 库使用时也可以是自定义组件
 * @method $this tip(string $value) 配置组件文案，例如加载中...
 * @method $this tipPlacement(string $value) 配置组件 tip 相对于 icon 的位置
 * @method $this delay(string $value) 配置组件显示延迟的时间（毫秒）
 * @method $this overlay(string $value) 配置组件显示 spinner 时是否显示遮罩层
 * @method $this body(string $value) 作为容器使用时，被包裹的内容
 * @method $this loadingConfig(string $value) 为 Spinner 指定挂载的容器, root 是一个 selector，在拥有Spinner的组件上都可以通过传递loadingConfig改变 Spinner 的挂载位置，开启后，会强制开启属性overlay=true，并且icon会失效
 * @method $this showOn(string $value) 是否显示 spinner 组件的表达式条件
 */
class Spinner extends BaseSchema
{
    public string $type = 'spinner';
}
