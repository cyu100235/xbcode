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
 * 表单项集合
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/fieldset
 * @method $this className(string $value) 设置 CSS 类名
 * @method $this headingClassName(string $value) 设置标题 CSS 类名
 * @method $this bodyClassName(string $value) 设置内容区域 CSS 类名
 * @method $this title(string $value) 设置标题
 * @method $this body(array $value) 设置表单项集合
 * @method $this mode(string $value) 设置展示模式，默认同 Form 中的模式
 * @method $this collapsable(bool $value) 设置是否可折叠
 * @method $this collapsed(bool $value) 设置默认是否折叠
 * @method $this collapseTitle(string $value) 设置收起的标题
 * @method $this size(string $value) 设置大小，支持 xs、sm、base、md、lg
 */
class FieldSet extends FormBase
{
    public string $type = 'fieldset';
}
