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

use plugin\xbCode\builder\Components\Status;
use plugin\xbCode\builder\Components\Table\TableColumn;

/**
 * 表格列组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait StatusColumn
{
    /**
     * 添加状态列
     * @param string $name
     * @param string $label
     * @param callable|array $option
     * @return TableColumn|Status
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnStatus(string $name, string $label, callable|array $option = [])
    {
        /** @var TableColumn|Status */
        $component = $this->useCustomColumn(Status::class, $name, $label, $option);
        $component->align('center');
        $component->vAlign('middle ');
        return $component;
    }
}
