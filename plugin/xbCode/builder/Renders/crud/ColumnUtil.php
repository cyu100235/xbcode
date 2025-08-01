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
namespace plugin\xbCode\builder\Renders\crud;

use plugin\xbCode\builder\Components\Table\TableColumn;

/**
 * 表格单元列
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait ColumnUtil
{
    /**
     * 表格列配置
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $columns = [];
    
    /**
     * 使用自定义组件列
     * @param string $type
     * @param string $name
     * @param string $title
     * @return TableColumn
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function useCustomColumn(string $type, string $name, string $title, callable|array $option = [])
    {
        if($type) {
            /** @var TableColumn */
            $component = new $type;
        } else {
            $component = new TableColumn;
        }
        $component->name($name);
        $component->label($title);
        if (is_array($option)) {
            // 如果是数组，则设置变量
            $component->setVariables($option);
        }else{
            $option($component);
        }
        $this->columns[] = $component;
        return $component;
    }

    /**
     * 添加表格列
     * @param string $name 列键名
     * @param string $title 列标题
     * @param array $option 列配置
     * @return TableColumn
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumn(string $name, string $title, callable|array $option = [])
    {
        $component = $this->useCustomColumn(TableColumn::class, $name, $title, $option);
        return $component;
    }

    /**
     * 获取表格列配置
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getColumns(): array
    {
        return $this->columns;
    }
}
