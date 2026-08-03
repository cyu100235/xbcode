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

use plugin\xbCode\builder\Components\Tag;
use plugin\xbCode\builder\Components\Table\TableColumn;

/**
 * 映射列组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait TagColumn
{
    /**
     * 添加标签列
     * @param string $name 字段名称
     * @param string $label 列标签
     * @param callable|array $option
     * @return TableColumn|Tag
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnTag(string $name, string $label, callable|array $option = [])
    {
        /** @var TableColumn|Tag */
        $component = $this->useCustomColumn(Tag::class, $name, $label, $option);
        $component->align('center');
        $component->color('active');
        $component->displayMode('normal');
        return $component;
    }
}
