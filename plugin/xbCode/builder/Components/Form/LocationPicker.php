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
 * 地理位置
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/location-picker
 * @method $this value(mixed $value) 选中值
 * @method $this vendor(string $value) 地图厂商，目前只实现了百度地图和高德地图
 * @method $this ak(string $value) 百度/高德地图的 ak
 * @method $this static(bool $value) 是否静态展示
 * @method $this clearable(bool $value) 输入框是否可清空
 * @method $this placeholder(string $value) 默认提示
 * @method $this autoSelectCurrentLoc(bool $value) 是否自动选中当前地理位置
 * @method $this onlySelectCurrentLoc(bool $value) 是否限制只能选中当前地理位置，设置为 true 后，可用于充当定位组件
 * @method $this coordinatesType(string $value) 坐标系类型，默认百度坐标，使用高德地图时应设置为'gcj02'， 高德地图不支持坐标转换
 * @method $this staticSchema(array $value) 静态展示，内嵌模式{static: true: embed: true}时的额外配置
 */
class LocationPicker extends FormBase
{
    public string $type = 'location-picker';
}
