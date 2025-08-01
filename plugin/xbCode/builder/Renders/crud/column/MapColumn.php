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
namespace plugin\xbCode\builder\Renders\crud\column;

use plugin\xbCode\builder\Components\Mapping;

/**
 * 映射列组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait MapColumn
{
    /**
     * 添加映射列
     * @param string $name 字段名称
     * @param string $label 列标签
     * @param array $mapping 映射关系
     * - `key` 映射键名
     * - `value` 映射数据（支持HTML）
     * @param callable|array $option
     * @return Mapping
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnMap(string $name, string $label, array $mapping, callable|array $option = [])
    {
        /** @var Mapping */
        $component = $this->useCustomColumn(Mapping::class, $name, $label, $option);
        $component->map($mapping);
        return $component;
    }
}
