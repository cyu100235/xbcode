<?php
namespace app\backend\controller;

use app\model\DictData;
use app\validate\DictValidate;
use xbcode\builder\FormBuilder;
use xbcode\builder\ListBuilder;
use xbcode\builder\table\attrs\RowEditTrait;
use xbcode\XbController;
use app\model\DictTag;
use support\Request;

/**
 * 字典管理
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class DictController extends XbController
{
    // 引入可编辑行
    use RowEditTrait;

    /**
     * 模型
     * @var 
     */
    protected $model;

    /**
     * 初始化
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function init()
    {
        parent::init();
        $this->model = new DictTag;
    }
    
    /**
     * 表格
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function indexTable(Request $request)
    {
        $builder = new ListBuilder;
        $builder->addActionOptions('操作', [
            'width' => 250
        ]);
        $builder->pageConfig();
        $builder->editConfig();
        $builder->addTopButton('add', '添加', [
            'type' => 'modal',
            'api' => xbUrl('Dict/add'),
            'path' => xbUrl('Dict/add'),
        ], [
            'title' => '添加字典',
        ], [
            'type' => 'primary',
        ]);
        $builder->addRightButton('dict', '数据管理', [
            'api' => xbUrl('DictData/index'),
            'path' => xbUrl('DictData/index'),
        ], [
            'title' => '数据管理',
        ], [
            'type' => 'success',
            'icon' => 'List',
        ]);
        $builder->addRightButton('edit', '修改', [
            'type' => 'modal',
            'api' => xbUrl('Dict/edit'),
            'path' => xbUrl('Dict/edit'),
        ], [
            'title' => '修改字典',
        ], [
            'type' => 'primary',
            'icon' => 'Edit',
        ]);
        $builder->addRightButton('del', '删除', [
            'type' => 'confirm',
            'api' => xbUrl('Dict/del'),
            'path' => xbUrl('Dict/del'),
            'method' => 'DELETE',
        ], [
            'type' => 'error',
            'title' => '温馨提示',
            'content' => '是否确认删除该数据？',
        ], [
            'type' => 'danger',
            'icon' => 'Delete',
        ]);
        $builder->addScreen('keyword', '$input', '关键词', '', [
            'placeholder' => '字典名称/字典标识',
        ]);
        $builder->addColumn('create_at', '创建时间', [
            'width' => 160,
        ]);
        $builder->addColumn('title', '字典名称');
        $builder->addColumn('name', '字典标识', [
            'params' => [
                'type' => 'tag'
            ],
        ]);
        $builder->addColumn('dict_num', '字典数据');
        $builder->addColumn('state', '站点状态', [
            'width' => 120,
            'params' => [
                'type' => 'switch',
                'api' => xbUrl('Dict/rowEdit'),
                'props' => [
                    'activeText' => '开启',
                    'inactiveText' => '停用',
                    'activeValue' => '20',
                    'inactiveValue' => '10',
                ],
            ]
        ]);
        $builder->addColumn('sort', '排序', [
            'width' => 80,
            'params' => [
                'type' => 'input',
                'api' => xbUrl('Dict/rowEdit'),
                'min' => 0,
            ],
        ]);
        $data = $builder->create();
        return $this->successRes($data);
    }
    
    /**
     * 列表
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        $keyword = $request->get('keyword');
        $where   = [];
        if ($keyword) {
            $where[] = ['title|name', 'like', "%{$keyword}%"];
        }
        $model = $this->model;
        $data  = $model
            ->where($where)
            ->order('sort asc,id desc')
            ->paginate()
            ->each(function ($item) {
                $dictNum = DictData::where('dict_id', $item['id'])->count();
                $item->dict_num = $dictNum;
                return $item;
            })
            ->toArray();
        return $this->successRes($data);
    }
    
    /**
     * 添加
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function add(Request $request)
    {
        if ($request->method() == 'POST') {
            // 获取数据
            $post = $request->post();
            // 数据验证
            xbValidate(DictValidate::class, $post, 'add');
            // 保存数据
            $model           = $this->model;
            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            // 返回结果
            return $this->success('保存成功');
        }
        $view = $this->formView()->setMethod('POST')->create();
        return $this->successRes($view);
    }
    
    /**
     * 修改
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function edit(Request $request)
    {
        $id    = $request->get('id');
        $model = $this->model;
        $model = $model->where('id', $id)->find();
        if (!$model) {
            return $this->fail('数据不存在');
        }
        if ($request->method() == 'PUT') {
            // 获取数据
            $post = $request->post();
            // 数据验证
            xbValidate(DictValidate::class, $post, 'edit');
            // 保存数据
            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            // 返回结果
            return $this->success('保存成功');
        }
        $view = $this->formView()
            ->setMethod('PUT')
            ->setData($model)
            ->create();
        return $this->successRes($view);
    }
    
    /**
     * 删除
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function del(Request $request)
    {
        $id    = $request->post('id');
        $model = $this->model;
        $model = $model->where('id', $id)->find();
        if (!$model) {
            return $this->fail('数据不存在');
        }
        if (!$model->delete()) {
            return $this->fail('删除失败');
        }
        // 返回结果
        return $this->success('删除成功');
    }

    /**
     * 获取表单视图
     * @return FormBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function formView()
    {
        $builder = new FormBuilder;
        $builder->addRow('title', 'input', '字典名称', '', [
            'prompt' => '字典名称，示例：性别'
        ]);
        $builder->addRow('name', 'input', '字典标识', '', [
            'prompt' => '字典标识，示例：sex'
        ]);
        $builder->addRow('state', 'radio', '字典状态', '20', [
            'prompt' => '选择字典状态',
            'options' => [
                ['label' => '开启', 'value' => '20'],
                ['label' => '停用', 'value' => '10'],
            ],
        ]);
        $builder->addRow('sort', 'input', '字典排序', '0', [
            'prompt' => '数字越小越靠前'
        ]);
        return $builder;
    }
}
