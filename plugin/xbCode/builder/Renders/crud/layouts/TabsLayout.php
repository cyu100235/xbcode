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
namespace plugin\xbCode\builder\Renders\crud\layouts;

use plugin\xbCode\builder\Components\Custom\XbTabs;

/**
 * 选项卡布局
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait TabsLayout
{
    /**
     * 自定义选项卡组件
     * @var XbTabs
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected XbTabs $tabs;
    
    /**
     * 使用选项卡组件
     * @param string $field
     * @return XbTabs
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function useTabs(string $field = '_tab')
    {
        $this->tabs->field($field);
        return $this->tabs;
    }
    
    /**
     * 添加选项卡(子项)
     * @param string $label
     * @param mixed $value
     * @param string $icon
     * @param array $props
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addTab(string $label, mixed $value, string $icon = '')
    {
        if (empty($this->tabs->items)) {
            $this->tabs->items([]);
        }
        if (count($this->tabs->items) <= $this->tabs->count) {
            array_push($this->tabs->items, [
                'label' => $label,
                'value' => $value,
                'icon'  => $icon,
            ]);
        }
        return $this;
    }
    
    /**
     * 添加选项卡(子项)
     * @param array $sidebars
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addTabs(array $items)
    {
        $items = array_slice($items, 0, $this->tabs->count);
        $this->tabs->items($items);
        return $this;
    }
    
    /**
     * 获取选项卡组件实例
     * @return XbTabs
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function getTabs()
    {
        // 返回组件实例
        return $this->tabs;
    }
}