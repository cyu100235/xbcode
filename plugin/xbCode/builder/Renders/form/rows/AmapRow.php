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
namespace plugin\xbCode\builder\Renders\form\rows;

use plugin\xbCode\builder\Components\Form\LocationPicker;

/**
 * 高德地图
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait AmapRow
{
    /**
     * 添加高德地图组件
     * @param string $field
     * @param string $title
     * @param mixed $value
     * @param callable|array $config
     * @return LocationPicker
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowAmapPicker(string $field, string $title, mixed $value = '', callable|array $option= [])
    {
        /** @var LocationPicker */
        $component = $this->addRow(LocationPicker::class, $field, $title, $value, $option);
        // 设置使用高德地图
        $component->vendor('gaode');
        // 设置坐标类型为高德地图的 gcj02 坐标
        $component->coordinatesType('gcj02');
        return $component;
    }
}
