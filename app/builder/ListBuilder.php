<?php

namespace app\builder;

use app\builder\table\AttrsTrait;
use app\builder\table\ButtonTrait;
use app\builder\table\CheckboxTrait;
use app\builder\table\ColumnTrait;
use app\builder\table\CustomTrait;
use app\builder\table\EditColumnTrait;
use app\builder\table\EventTrait;
use app\builder\table\ExpandTrait;
use app\builder\table\ExportTrait;
use app\builder\table\FilterTrait;
use app\builder\table\FormTrait;
use app\builder\table\ImportTrait;
use app\builder\table\MenuTrait;
use app\builder\table\PagesTrait;
use app\builder\table\PrintTrait;
use app\builder\table\RadioTrait;
use app\builder\table\RealTableTrait;
use app\builder\table\ResizableTrait;
use app\builder\table\RowTrait;
use app\builder\table\SortTrait;
use app\builder\table\TabsTrait;
use app\builder\table\ToolbarTrait;
use app\builder\table\TooltipTrait;
use app\builder\table\TreeTrait;

/**
 * 表格构造器
 *
 * @author 贵州小白基地网络科技有限公司
 * @Email cy958416459@qq.com
 * @DateTime 2023-02-27
 */
class ListBuilder
{
    // 工具类
    use AttrsTrait,
        ButtonTrait,
        CheckboxTrait,
        ColumnTrait,
        CustomTrait,
        EditColumnTrait,
        ExpandTrait,
        ExportTrait,
        FilterTrait,
        FormTrait,
        ImportTrait,
        MenuTrait,
        PagesTrait,
        PrintTrait,
        RadioTrait,
        RealTableTrait,
        ResizableTrait,
        RowTrait,
        SortTrait,
        TabsTrait,
        ToolbarTrait,
        TooltipTrait,
        TreeTrait;

    // 在每个对象的静态缓存中存储现有属性。
    protected static $cache = [];

    /**
     * 解析表格规则
     * @Author 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-02-27
     * @return array
     */
    private function parseRule(): array
    {
        return get_object_vars($this);
    }

    /**
     * 获取表格JSON规则
     * @return \think\response\Json
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     * @email cy958416459@qq.com
     */
    public function JSONRule()
    {
        return json($this->parseRule());
    }

    /**
     * 返回表格规则
     *
     * @Author 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-02-27
     * @return array
     */
    public function create(): array
    {
        $resutl = $this->parseRule();
        // 实时表格
        if (isset($resutl['realTable'])) {
            $data['realTable'] = $resutl['realTable'];
            unset($resutl['realTable']);
        }
        // 筛选查询
        if (!empty($resutl['formConfig'])) {
            array_push(
                $resutl['formConfig']['items'],
                [
                    'itemRender' => [
                        'name' => '$buttons',
                        'children'  => $resutl['screenConfig']
                    ],
                ]
            );
        }
        $data['tableConfig'] = $resutl;
        // 移除按钮属性
        if (isset($data['tableConfig']['topButtonList'])) {
            unset($data['tableConfig']['topButtonList']);
        }
        if (isset($data['tableConfig']['extraButtonList'])) {
            unset($data['tableConfig']['extraButtonList']);
        }
        if (isset($data['tableConfig']['rightButtonList'])) {
            unset($data['tableConfig']['rightButtonList']);
        }
        if (isset($data['tabsConfig']['tabsConfig'])) {
            unset($data['tabsConfig']['tabsConfig']);
        }
        $data['tabsConfig']             = $resutl['tabsConfig'];
        $data['topButtonList']          = $resutl['topButtonList'];
        $data['extraButtonList']        = $resutl['extraButtonList'];
        $data['rightButtonList']        = $resutl['rightButtonList'];
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