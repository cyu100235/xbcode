<?php

namespace app\common\trait;

use think\Request;
use think\Response;

trait MenusTrait
{
    /**
     * 项目ID
     * @var int
     */
    protected $saas_appid = null;
    public function indexTable(Request $request)
    {
        $builder = new ListBuilder;
        $data    = $builder
            ->addActionOptions('操作', [
                'width' => 180
            ])
            ->editConfig()
            ->treeConfig([
                'rowField' => 'id',
            ])
            ->addTopButton('add', '添加', [
                'api' => $this->pluginPrefix . '/admin/Menus/add',
                'path' => '/Menus/add'
            ], [], [
                'type' => 'primary'
            ])
            ->addRightButton('edit', '修改', [
                'api' => $this->pluginPrefix . '/admin/Menus/edit',
                'path' => '/Menus/edit'
            ], [], [
                'type' => 'primary',
            ])
            ->addRightButton('del', '删除', [
                'type' => 'confirm',
                'api' => $this->pluginPrefix . '/admin/Menus/del',
                'method' => 'delete',
            ], [
                'type' => 'error',
                'title' => '温馨提示',
                'content' => '是否确认删除该数据',
            ], [
                'type' => 'danger',
            ])
            ->addColumn('path', '权限地址', [
                'treeNode' => true
            ])
            ->addColumn('title', '权限名称')
            ->addColumnEle('show', '是否显示', [
                'width' => 100,
                'params' => [
                    'type' => 'tags',
                    'options' => ShowStatus::dictOptions(),
                    'style' => ShowStatusStyle::parseAlias('type'),
                ],
            ])
            ->addColumnEle('is_api', '是否接口', [
                'width' => 100,
                'params' => [
                    'type' => 'tags',
                    'options' => YesNoEum::dictOptions(),
                    'style' => YesNoEumStyle::parseAlias('type'),
                ],
            ])
            ->addColumnEle('component', '组件类型', [
                'width' => 120,
                'params' => [
                    'type' => 'tags',
                    'options' => AuthRuleRuleType::dictOptions(),
                    'style' => AuthRuleRuleTypeStyle::parseAlias('type', false),
                ],
            ])
            ->addColumn('method', '请求类型', [
                'width' => 180
            ])
            ->addColumnEdit('sort', '排序', [
                'width' => 100,
                'params' => [
                    'type' => 'input',
                    'api' => $this->pluginPrefix . '/admin/Menus/rowEdit',
                    'min' => 0,
                ],
            ])
            ->create();
        return $this->successRes($data);
    }
    public function index(Request $request)
    {
    }
    public function add(Request $request)
    {
    }
    public function edit(Request $request)
    {
    }
    public function del(Request $request)
    {
    }
}
