<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Components;

/**
 * 宫格布局
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/grid
 * @method $this name(string $name) 组件名称
 * @method $this className(string $value) 外层 CSS 类名
 * @method $this style(array $value) 元素CSS样式
 * @method $this columns(array $value) 列集合
 * - xs（宽度占比： 1 - 12）: 12 栅格，移动端默认 12 栅格
 * - sm（宽度占比： 1 - 12）: 6 栅格，移动端默认 6 栅格
 * - md（宽度占比： 1 - 12）: 3 栅格，移动端默认 3 栅格
 * - lg（宽度占比： 1 - 12）: 2 栅格，移动端默认 2 栅格
 * - valign: 'top' | 'middle' | 'bottom' | 'between'
 * @method $this gap(string $value) 水平间距
 * - 'xs' | 'sm' | 'base' | 'none' | 'md' | 'lg'
 * @method $this valign(string $value) 垂直对齐方式
 * - 'top' | 'middle' | 'bottom' | 'between'
 * @method $this align(string $value) 水平对齐方式
 * - 'left' | 'right' | 'between' | 'center'
 */
class GridSchema extends BaseSchema
{
    public string $type = 'grid';

    /**
     * 列集合
     * @var array
     */
    public $columns = [];

    /**
     * 超小屏（移动端）宽度占比，1 - 12，默认 12 栅格
     * @param string|int $value 宽度占比（1 - 12）或 'auto'
     * @return static
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function xs(string|int $value = 'auto')
    {
        $this->columns[0]['xs'] = $value;
        return $this;
    }

    /**
     * 小屏宽度占比，1 - 12，默认 6 栅格
     * @param string|int $value 宽度占比（1 - 12）或 'auto'
     * @return static
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function sm(string|int $value = 'auto')
    {
        $this->columns[0]['sm'] = $value;
        return $this;
    }

    /**
     * 中屏宽度占比，1 - 12，默认 3 栅格
     * @param string|int $value 宽度占比（1 - 12）或 'auto'
     * @return static
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function md(string|int $value = 'auto')
    {
        $this->columns[0]['md'] = $value;
        return $this;
    }

    /**
     * 大屏宽度占比，1 - 12，默认 2 栅格
     * @param string|int $value 宽度占比（1 - 12）或 'auto'
     * @return static
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function lg(string|int $value = 'auto')
    {
        $this->columns[0]['lg'] = $value;
        return $this;
    }

    /**
     * 当前列内容的垂直对齐
     * @param string|int $value
     * @return static
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function valign(string|int $value = 'top')
    {
        $this->columns[0]['valign'] = $value;
        return $this;
    }
    
    /**
     * 添加组件列
     * @param array $components 组件合计
     * @return static
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumns(array $components)
    {
        $this->columns[0]['body'] = $components;
        $this->style([
            'padding-bottom' => '20px',
        ]);
        return $this;
    }
}
