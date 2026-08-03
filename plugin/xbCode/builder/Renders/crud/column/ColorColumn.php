<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Renders\crud\column;

use plugin\xbCode\builder\Components\Color;
use plugin\xbCode\builder\Components\Table\TableColumn;

/**
 * 颜色列
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait ColorColumn
{
    /**
     * 添加颜色列
     * @param string $name
     * @param string $label
     * @param callable|array $option
     * @return TableColumn|Color
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnColor(string $name, string $label, callable|array $option = [])
    {
        /** @var TableColumn|Color */
        $component = $this->useCustomColumn(Color::class, $name, $label, $option);
        return $component;
    }
}
