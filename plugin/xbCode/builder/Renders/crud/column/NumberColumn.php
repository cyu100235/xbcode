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

use plugin\xbCode\builder\Components\Form\InputNumber;
use plugin\xbCode\builder\Components\Table\TableColumn;

/**
 * 数字输入列
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait NumberColumn
{
    /**
     * 添加数字列
     * @param string $name
     * @param string $label
     * @param callable|array $option
     * @return TableColumn|InputNumber
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnNumber(string $name, string $label, callable|array $option = [])
    {
        /** @var TableColumn|InputNumber */
        $component = $this->useCustomColumn(InputNumber::class, $name, $label, $option);
        return $component;
    }
}
