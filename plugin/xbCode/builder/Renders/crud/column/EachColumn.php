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

use plugin\xbCode\builder\Components\Tpl;
use plugin\xbCode\builder\Components\Each;
use plugin\xbCode\builder\Components\Table\TableColumn;

/**
 * 循环组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait EachColumn
{
    /**
     * 添加循环列
     * @param string $name 字段名称
     * @param string $label 列标签
     * @param string $template 循环模板
     * @param callable|array $option
     * @return Each|TableColumn
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnEach(string $name, string $label, ?string $template = null, callable|array $option = [])
    {
        /** @var TableColumn|Each */
        $component = $this->useCustomColumn(Each::class, $name, $label, $option);
        if (!$template) {
            $template = "<span class='label label-info m-l-sm'>\${item}</span>";
        }
        $tplComponent = Tpl::make();
        $tplComponent->tpl($template);
        $component->items($tplComponent);
        return $component;
    }
}
