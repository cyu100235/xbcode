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
namespace plugin\xbCode\builder\Renders\form\rows;

use plugin\xbCode\builder\Components\Form\ListSelect;

/**
 * 列表选择表单项
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait ListSelectRow
{    
    /**
     * 添加列表选择组件
     * @param string $name 字段名
     * @param string $title 标题
     * @param mixed $value 默认值
     * @param callable|array $option 组件参数
     * @return ListSelect
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowListSelect(string $name, string $title, mixed $value = '', callable|array $option = [])
    {
        /** @var ListSelect */
        $component = $this->addRow(ListSelect::class, $name, $title, $value, $option);
        $component->imageClassName('thumb-lg');
        return $component;
    }
}
