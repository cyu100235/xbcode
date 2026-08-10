<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Renders\crud\column;

use plugin\xbCode\builder\Components\Form\Select;
use plugin\xbCode\builder\Components\Table\TableColumn;

/**
 * 下拉选择框列列
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait SelectColumn
{
    /**
     * 添加下拉选择框列
     * @param string $name
     * @param string $label
     * @param array $quickEdit
     * @param callable|array $option
     * @throws \Exception
     * @return TableColumn|Select
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnSelect(string $name, string $label, array $quickEdit = [], callable|array $option = [])
    {
        if (empty($this->useCRUD()->quickSaveItemApi)) {
            throw new \Exception('请先设置【quickSaveItemApi】接口地址');
        }
        /** @var TableColumn|Select */
        $component = $this->addColumn($name, $label, $option);
        $component->quickEdit([
            'type' => 'select',
            'saveImmediately' => true,
            'mode' => 'inline',
            'size' => 'md',
            ...$quickEdit,
        ]);
        return $component;
    }
}
