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
 * JSON 编辑器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/json-schema-editor
 * @method $this rootTypeMutable(bool $value) 顶级类型是否可配置
 * @method $this showRootInfo(bool $value) 是否显示顶级类型信息
 * @method $this disabledTypes(array $value) 用来禁用默认数据类型，默认类型有：string、number、interger、object、number、array、boolean、null
 * @method $this definitions(array $value) 用来配置预设类型
 * @method $this mini(bool $value) 用来开启迷你模式，适应于边栏面板，宽度较低的情况
 * @method $this placeholder(array $value) 属性输入控件的占位提示文本
 */
class JSONSchemaEditor extends FormBase
{
    public string $type = 'json-schema-editor';
}
