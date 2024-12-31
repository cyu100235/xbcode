<?php

namespace xbcode\builder;

use xbcode\builder\table\attrs\DescTrait;
use xbcode\builder\table\attrs\TopTrait;
use xbcode\builder\table\buttons\ExtraButtonTrait;
use xbcode\builder\table\buttons\RightButtonTrait;
use xbcode\builder\table\buttons\TopButtonTrait;
use xbcode\builder\table\attrs\CheckboxTrait;
use xbcode\builder\table\attrs\CustomTrait;
use xbcode\builder\table\attrs\EditConfigTrait;
use xbcode\builder\table\attrs\ExportTrait;
use xbcode\builder\table\attrs\FilterTrait;
use xbcode\builder\table\attrs\FormTrait;
use xbcode\builder\table\attrs\ImportTrait;
use xbcode\builder\table\attrs\MenuTrait;
use xbcode\builder\table\attrs\PrintTrait;
use xbcode\builder\table\attrs\RadioTrait;
use xbcode\builder\table\attrs\RealTableTrait;
use xbcode\builder\table\attrs\ResizableTrait;
use xbcode\builder\table\attrs\RowEditTrait;
use xbcode\builder\table\attrs\RowTrait;
use xbcode\builder\table\attrs\SortTrait;
use xbcode\builder\table\attrs\TabsTrait;
use xbcode\builder\table\attrs\ToolbarTrait;
use xbcode\builder\table\attrs\TreeTrait;
use xbcode\builder\table\attrs\ButtonTrait;
use xbcode\builder\table\attrs\ColumnTrait;
use xbcode\builder\table\attrs\ExpandTrait;
use xbcode\builder\table\attrs\PageTrait;
use xbcode\builder\table\attrs\BaseTrait;
use xbcode\builder\table\DataUtils;

/**
 * 表格构造器
 * @author 贵州小白基地网络科技有限公司
 * @Email cy958416459@qq.com
 * @DateTime 2023-02-27
 */
class ListBuilder
{
    // 表格基础参数
    use BaseTrait;
    // 表格顶部按钮
    use TopButtonTrait;
    // 表格顶部扩展按钮
    use ExtraButtonTrait;
    // 表格右侧按钮
    use RightButtonTrait;
    // 按钮通用方法
    use ButtonTrait;
    // 表格分页
    use PageTrait;
    // 表格列操作
    use ColumnTrait;
    // 自定义配置
    use CustomTrait;
    // 复选框
    use CheckboxTrait;
    // 编辑配置
    use EditConfigTrait;
    // 展开配置
    use ExpandTrait;
    // 导出配置
    use ExportTrait;
    // 排序配置
    use FilterTrait;
    // 筛选查询配置
    use FormTrait;
    // 顶部组件配置
    use TopTrait;
    // 导入配置
    use ImportTrait;
    // 菜单配置
    use MenuTrait;
    // 打印配置
    use PrintTrait;
    // 单选配置
    use RadioTrait;
    // 实时表格配置
    use RealTableTrait;
    // 调整大小配置
    use ResizableTrait;
    // 单元编辑配置
    use RowEditTrait;
    // 表格行配置
    use RowTrait;
    // 排序配置
    use SortTrait;
    // 选项卡配置
    use TabsTrait;
    // 工具栏配置
    use ToolbarTrait;
    // 表格树配置
    use TreeTrait;
    // 表格描述配置
    use DescTrait;

    /**
     * 解析表格规则
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function parseRule(): array
    {
        return get_object_vars($this);
    }
    
    /**
     * 获取表格JSON规则
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function JSONRule()
    {
        return json($this->parseRule());
    }
    
    /**
     * 创建表格
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function create(): array
    {
        // 解析表格规则
        $result = $this->parseRule();
        // 处理实时表格
        $realTable = DataUtils::realTable($result);
        // 选项卡表格
        $tabsConfig = DataUtils::tabsConfig($result);
        // 处理表格顶部按钮
        $topButton = DataUtils::topButton($result);
        // 处理表格扩展按钮
        $extraButton = DataUtils::extraButton($result);
        // 处理表格右侧按钮
        $rightButton = DataUtils::rightButton($result);
        // 筛选查询配置
        if (!empty($result['formConfig'])) {
            $collapseNode = array_filter($result['formConfig']['items'], function ($item) {
                $folding = $item['folding'] ?? false;
                return $folding === true;
            });
            array_push(
                $result['formConfig']['items'],
                [
                    'collapseNode' => count($collapseNode) > 0,
                    'itemRender' => [
                        'name' => 'VxeButtonGroup',
                        'options'  => $result['screenConfig']
                    ],
                ]
            );
        }
        // 开启批量操作
        if (!empty($extraButton)) {
            array_unshift($result['columns'],[
                'type' => 'checkbox',
                'width' => 40,
            ]);
        }
        // 开启实时表格
        // 组装表格规则
        $data        = [
            // 表格配置
            'config' => $result,
            // 表格描述
            'headerDesc' => $result['headerDesc'],
            // 实时表格
            'realTable' => $realTable,
            // 选项卡表格
            'tabsConfig' => $tabsConfig,
            // 表格顶部按钮
            'topButtons' => $topButton,
            // 表格顶部扩展按钮
            'extraButtons' => $extraButton,
            // 表格右侧按钮
            'rightButtons' => $rightButton,
        ];
        // 返回数据
        return $data;
    }

    /**
     * 动态设置属性
     * @param mixed $name
     * @param mixed $value
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-04-29
     */
    public function __set($name, $value)
    {
        $this->$name = $value;
    }
}