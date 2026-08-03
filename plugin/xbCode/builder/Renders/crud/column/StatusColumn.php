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
     * @param string $name 列名
     * @param string $label 状态标签
     * @param array $maps 状态映射
     * @param callable|array $option 选项
     * @return TableColumn|Status
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnStatus(string $name, string $label, array $maps, callable|array $option = [])
    {
        /** @var TableColumn|Status */
        $component = $this->useCustomColumn(Status::class, $name, $label, $option);
        $component->type('mapping');
        $component->align('center');
        $component->vAlign('middle ');
        $component->map([
            '*' => [
                'type' => 'status',
                'source' => $maps,
            ],
        ]);
        return $component;
    }
}
