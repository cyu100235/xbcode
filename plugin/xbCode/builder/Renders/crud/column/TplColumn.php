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

use plugin\xbCode\builder\Components\Tpl;
use plugin\xbCode\builder\Components\Table\TableColumn;

/**
 * 模板列组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait TplColumn
{
    /**
     * 添加模板列
     * @param string $name
     * @param string $label
     * @param string $tpl
     * @param callable|array $option
     * @return TableColumn|Tpl
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnTpl(string $name, string $label,string $tpl, callable|array $option = [])
    {
        /** @var TableColumn|Tpl */
        $component = $this->useCustomColumn(Tpl::class, $name, $label, $option);
        $component->tpl($tpl);
        return $component;
    }
}
