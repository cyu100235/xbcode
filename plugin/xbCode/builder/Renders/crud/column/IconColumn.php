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

use plugin\xbCode\builder\Components\Custom\XbIcon;

/**
 * 表格列组件
 * @copyright 贵州猿创科技有限公司
 * @author 楚羽幽 416716328@qq.com
 */
trait IconColumn
{
    /**
     * 添加图标列
     * @param string $name
     * @param string $label
     * @param callable|array $option
     * @return XbIcon
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnIcon(string $name, string $label, callable|array $option = [])
    {
        /** @var XbIcon */
        $component = $this->useCustomColumn(XbIcon::class, $name, $label, $option);
        return $component;
    }
}
