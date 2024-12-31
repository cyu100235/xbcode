<?php
namespace app\backend\controller;

use app\model\DictData;
use app\model\DictTag;
use app\validate\DictDataValidate;
use xbcode\builder\FormBuilder;
use xbcode\builder\ListBuilder;
use xbcode\builder\table\attrs\RowEditTrait;
use xbcode\XbController;
use support\Request;

/**
 * 字典数据管理
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class DictDataController extends XbController
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
        $this->model = new DictData;
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
        $dict_id = $request->get('id');
        $builder = new ListBuilder;
        $builder->addActionOptions('操作', [
            'width' => 150
        ]);
        $builder->pageConfig();
        $builder->editConfig();
        $builder->addTopButton('add', '添加', [
            'type' => 'modal',
            'api' => xbUrl('DictData/add'),
            'path' => xbUrl('DictData/add'),
            'queryParams' => [
                'dict_id' => $dict_id
            ],
        ], [
            'title' => '添加字典数据',
        ], [
            'type' => 'primary',
        ]);
        $builder->addRightButton('edit', '修改', [
            'type' => 'modal',
            'api' => xbUrl('DictData/edit'),
            'path' => xbUrl('DictData/edit'),
        ], [
            'title' => '修改字典数据',
        ], [
            'type' => 'primary',
            'icon' => 'Edit',
        ]);
        $builder->addRightButton('del', '删除', [
            'type' => 'confirm',
            'api' => xbUrl('DictData/del'),
            'path' => xbUrl('DictData/del'),
            'method' => 'DELETE',
        ], [
            'type' => 'error',
            'title' => '温馨提示',
            'content' => '是否确认删除该数据？',
        ], [
            'type' => 'danger',
            'icon' => 'Delete',
        ]);
        $builder->addColumn('create_at', '创建时间', [
            'width' => 160,
        ]);
        $builder->addColumn('label', '数据名称');
        $builder->addColumn('value', '数据参数');
        $builder->addColumn('state', '数据状态', [
            'width' => 120,
            'params' => [
                'type' => 'switch',
                'api' => xbUrl('DictData/rowEdit'),
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
                'api' => xbUrl('DictData/rowEdit'),
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
        $dictId = $request->get('id');
        $where   = [
            'dict_id' => $dictId
        ];
        $model = $this->model;
        $data  = $model
            ->where($where)
            ->order('sort asc,id asc')
            ->paginate()
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
        $dictId = $request->get('dict_id');
        if ($request->method() == 'POST') {
            $post = $request->post();
            $post['dict_id'] = $dictId;
            // 数据验证
            xbValidate(DictDataValidate::class, $post, 'add');
            $model           = $this->model;
            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            // 返回结果
            return $this->success('保存成功');
        }
        $builder = $this->formView($dictId);
        $builder = $builder->setMethod('POST');
        $data = $builder->create();
        return $this->successRes($data);
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
            $post = $request->post();
            // 数据验证
            xbValidate(DictDataValidate::class, $post, 'edit');
            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            // 返回结果
            return $this->success('保存成功');
        }
        $builder = $this->formView($model->dict_id);
        $builder->setMethod('PUT');
        $builder->setData($model);
        $data = $builder->create();
        return $this->successRes($data);
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
    private function formView($dictId)
    {
        $dictTitle = DictTag::where('id', $dictId)->value('title');
        $builder = new FormBuilder;
        $builder->addRow('title', 'xbInfo', '字典名称', $dictTitle);
        $builder->addRow('label', 'input', '数据名称', '', [
            'prompt' => '数据名称，示例：男生'
        ]);
        $builder->addRow('value', 'input', '数据参数', '', [
            'prompt' => '数据参数，示例：10'
        ]);
        $builder->addRow('state', 'radio', '字典状态', '20', [
            'col' => 12,
            'prompt' => '选择字典状态',
            'options' => [
                ['label' => '开启', 'value' => '20'],
                ['label' => '停用', 'value' => '10'],
            ],
        ]);
        $builder->addRow('sort', 'input', '字典排序', '0', [
            'col' => 12,
            'prompt' => '数字越小越靠前'
        ]);
        return $builder;
    }
}
