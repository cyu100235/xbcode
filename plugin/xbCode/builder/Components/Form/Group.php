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
 * 表单项组
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/group
 * @method $this className(string $value) 设置 CSS 类名
 * @method $this label(string $value) 设置 group 的标签
 * @method $this body(mixed $value) 设置表单项集合
 * @method $this mode(string $value) 设置展示模式，默认同 Form 中的模式
 * @method $this gap(string $value) 设置表单项之间的间距，可选：xs、sm、normal
 * @method $this direction(string $value) 设置展示方向，可以配置水平展示还是垂直展示。对应的配置项分别是：vertical、horizontal
 */
class Group extends FormBase
{
    public string $type = 'group';

    public function directionVertical()
    {
        $this->direction('vertical');
        return $this;
    }
}
