<?php
namespace app\backend\controller;

use support\Request;
use app\model\WebNotice;
use xbcode\builder\FormBuilder;
use xbcode\builder\ListBuilder;
use app\validate\WebNoticeValidate;
use xbcode\builder\table\attrs\RowEditTrait;
use xbcode\providers\DictProvider;
use xbcode\XbController;

/**
 * 站点公告管理
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class WebNoticeController extends XbController
{
    // 行编辑
    use RowEditTrait;
    
    /**
     * 模型
     * @var WebNotice
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
        $this->model = new WebNotice;
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
        // 表格渲染
        $builder = new ListBuilder;
        $builder->addActionOptions('操作', [
            'width' => 180
        ]);
        $builder->pageConfig();
        $builder->addTopButton('add', '发布公告', [
            'type' => 'modal',
            'api' => xbUrl('WebNotice/add'),
            'path' => xbUrl('WebNotice/add'),
        ], [
            'title' => '发布站点公告',
            'customStyle' => [
                'width' => '55%',
                'height' => '75vh',
            ],
        ], [
            'type' => 'primary',
            'icon' => 'Plus'
        ]);
        $builder->addRightButton('edit', '修改', [
            'type' => 'modal',
            'api' => xbUrl('WebNotice/edit'),
            'path' => xbUrl('WebNotice/edit'),
        ], [
            'title' => '修改公告',
            'customStyle' => [
                'width' => '55%',
                'height' => '75vh',
            ],
        ], [
            'type' => 'primary',
            'icon' => 'EditOutlined'
        ]);
        $builder->addRightButton('del', '删除', [
            'type' => 'confirm',
            'api' => xbUrl('WebNotice/del'),
            'path' => xbUrl('WebNotice/del'),
            'method' => 'DELETE',
        ], [
            'type' => 'error',
            'title' => '温馨提示',
            'content' => '是否确认删除该数据？',
        ], [
            'type' => 'danger',
            'icon' => 'DeleteOutlined'
        ]);
        $keyword = $request->get('keyword');
        $builder->addScreen('keyword', '$input', '站点标题', $keyword);
        $builder->addColumn('id', '序号', [
            'width' => 80,
        ]);
        $builder->addColumn('title', '公告标题', [
            'minWidth' => 180,
        ]);
        $builder->addColumn('state', '公告状态', [
            'width' => 120,
            'params' => [
                'type' => 'switch',
                'api' => xbUrl('WebNotice/rowEdit'),
                'props' => [
                    'activeText' => '已发布',
                    'inactiveText' => '已下架',
                    'activeValue' => '20',
                    'inactiveValue' => '10',
                ],
            ]
        ]);
        $builder->addColumn('create_at', '发布时间', [
            'width' => 180,
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
        $data = WebNotice::order('id desc')->paginate();
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
            $post = $request->post();

            // 数据验证
            xbValidate(WebNoticeValidate::class, $post, 'add');

            $model = new WebNotice;
            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            return $this->success('保存成功');
        }
        $builder = $this->formView();
        $builder->setMethod('POST');
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
        $model = WebNotice::where('id', $id)->find();
        if (!$model) {
            return $this->fail('该数据不存在');
        }
        if ($request->method() == 'PUT') {
            $post = $request->post();

            // 数据验证
            xbValidate(WebNoticeValidate::class, $post, 'edit');

            // 空密码，不修改
            if (empty($post['password'])) {
                unset($post['password']);
            }
            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            return $this->success('保存成功');
        }
        $builder = $this->formView();
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
        $id = $request->post('id');
        $model = WebNotice::where('id', $id)->find();
        if (!$model) {
            return $this->fail('该数据不存在');
        }
        if (!$model->delete()) {
            return $this->fail('删除失败');
        }
        return $this->success('删除成功');
    }

    /**
     * 表单
     * @return FormBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function formView()
    {
        $builder = new FormBuilder;
        $builder->addRow('title', 'input', '公告标题', '', [
            'col' => 12,
        ]);
        $builder->addRow('state', 'radio', '公告状态', '20', [
            'col' => 12,
            'options' => DictProvider::get('switchState')->options(),
        ]);
        $builder->addRow('content', 'xbWangEditor', '公告内容', '', [
            'col' => 24,
        ]);
        return $builder;
    }
}
