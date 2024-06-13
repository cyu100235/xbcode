<?php
namespace app\admin\controller;

use app\admin\validate\DictValidate;
use app\common\builder\FormBuilder;
use app\common\builder\ListBuilder;
use app\common\builder\table\RowEditTrait;
use hg\apidoc\annotation as Apidoc;
use app\common\XbController;
use support\Request;
use app\model\Dict;

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
        $this->model = new Dict;
    }

    /**
     * 字典表格
     * @Apidoc\Method ("GET")
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function indexTable(Request $request)
    {
        $builder = new ListBuilder;
        $builder->addActionOptions('操作', [
            'width' => 150
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
            'method' => 'DELETE',
        ], [
            'type' => 'error',
            'title' => '温馨提示',
            'content' => '是否确认删除该数据？',
        ], [
            'type' => 'danger',
            'icon' => 'Delete',
        ]);
        $builder->addScreen('keyword', 'input', '关键词', [
            'placeholder' => '字典名称/字典标识',
        ]);
        $builder->addColumn('create_at', '创建时间', [
            'width' => 160,
        ]);
        $builder->addColumn('title', '字典名称', [
            'width' => 150
        ]);
        $builder->addColumnEle('preview', '调用代码', [
            'width' => 300,
            'titlePrefix' => [
                'content' => '请自行导入命名空间：use app\common\providers\DictProvider',
            ],
            'params' => [
                'type' => 'html'
            ],
        ]);
        $builder->addColumnEle('content', '字典数据', [
            'params' => [
                'type' => 'html'
            ],
        ]);
        $builder->addColumnEdit('sort', '排序', [
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
     * 字典列表
     * @Apidoc\Method ("GET")
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        $page    = (int) $request->get('page', 1);
        $limit   = (int) $request->get('limit', 10);
        $keyword = $request->get('keyword');
        $where   = [];
        if ($keyword) {
            $where[] = ['title|name', 'like', "%{$keyword}%"];
        }
        $model = $this->model;
        $data  = $model
            ->where($where)
            ->order('sort asc,id desc')
            ->paginate([
                'list_rows' => $limit,
                'page' => $page,
            ])
            ->each(function ($item) {
                $item->preview = '<pre style="color:#13ce66;"><code>DictProvider::get(\'' . $item->name . '\')</code></pre>';
            })
            ->toArray();
        return $this->successRes($data);
    }

    /**
     * 添加字典
     * @Apidoc\Method ("GET,POST")
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function add(Request $request)
    {
        if ($request->method() == 'POST') {
            $post = $request->post();
            xbValidate(DictValidate::class, $post, 'add');
            // 处理换行符
            $post['content'] = str_replace("\n", "|", $post['content']);
            $model           = $this->model;
            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            // 缓存所有字典数据
            Dict::cacheDict();
            // 返回结果
            return $this->success('保存成功');
        }
        $view = $this->formView()->setMethod('POST')->create();
        return $this->successRes($view);
    }

    /**
     * 修改字典
     * @Apidoc\Method ("GET,PUT")
     * @param \support\Request $request
     * @return mixed
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
        // 处理换行符转换
        $model->content = str_replace("|", "\n", $model->content);
        if ($request->method() == 'PUT') {
            $post = $request->post();
            xbValidate(DictValidate::class, $post, 'edit');
            // 处理换行符
            $post['content'] = str_replace("\n", "|", $post['content']);
            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            // 缓存所有字典数据
            Dict::cacheDict();
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
     * 删除字典
     * @Apidoc\Method ("GET")
     * @param \support\Request $request
     * @return mixed
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
        // 缓存所有字典数据
        Dict::cacheDict();
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
            'col' => 12,
            'prompt' => '字典名称，示例：性别'
        ]);
        $builder->addRow('name', 'input', '字典标识', '', [
            'col' => 12,
            'prompt' => '字典标识，示例：sex'
        ]);
        $builder->addRow('content', 'textarea', '字典数据', '', [
            'resize' => 'none',
            'rows' => 15,
            'prompt' => '一行一条字典，数据示例：10=男',
        ]);
        return $builder;
    }
}
