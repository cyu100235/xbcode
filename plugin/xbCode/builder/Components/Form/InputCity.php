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
 * 城市选择器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-city
 * @method $this itemClassName(string $value) 子项类名称
 * @method $this allowCity(string $value) 是否允许选择城市，默认 true
 * @method $this allowDistrict(string $value) 是否允许选择区域，默认 true
 * @method $this searchable(bool $value) 是否出搜索框，默认 false
 * @method $this extractValue(bool $value) 是否抽取值，默认 true。如果设置成 false 值格式会变成对象，包含 code、province、city 和 district 文字信息
 */
class InputCity extends FormBase
{
    public string $type = 'input-city';
}
