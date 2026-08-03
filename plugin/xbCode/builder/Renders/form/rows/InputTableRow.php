<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Renders\form\rows;

use plugin\xbCode\builder\Components\Form\InputTable;

/**
 * 表单表格项
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait InputTableRow
{
    /**
     * 添加表格
     * @param string $field
     * @param string $title
     * @param mixed $value
     * @param callable|array $option
     * @return InputTable
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowTable(string $field, string $title, callable $callback)
    {
        /** @var InputTable */
        $component = $this->addRow(InputTable::class, $field, $title);
        $callback($component);
        return $component;
    }
}
