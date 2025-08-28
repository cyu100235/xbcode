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
 * 代码编辑器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/editor
 * @method $this language(string $value) 编辑器高亮的语言，支持通过 ${xxx} 变量获取
 * @method $this size(string $value) 编辑器高度，取值可以是 md、lg、xl、xxl
 * @method $this allowFullscreen(string $value) 是否显示全屏模式开关
 * @method $this options(object $value) monaco 编辑器配置，比如是否显示行号，不过无法设置 readOnly，只读模式需要使用 disabled
 * @method $this placeholder(string $value) 占位描述，没有值的时候展示
 * @method $this disabled(bool $value) 是否只读
 */
class Editor extends FormBase
{
    public string $type = 'editor';
}
