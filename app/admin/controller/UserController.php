<?php
namespace app\admin\controller;

use app\common\builder\FormBuilder;
use app\common\builder\ListBuilder;
use app\common\builder\table\RowEditTrait;
use app\common\providers\DictProvider;
use hg\apidoc\annotation as Apidoc;
use app\common\XbController;
use Webman\Event\Event;
use support\Request;
use app\model\User;

/**
 * 用户管理
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class UserController extends XbController
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
        $this->model = new User;
    }

    /**
     * 表格
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
            'width' => 200
        ]);
        $builder->pageConfig();
        $builder->editConfig();
        $builder->addTopButton('add', '添加', [
            'type' => 'modal',
            'api' => xbUrl('User/add'),
            'path' => xbUrl('User/add'),
        ], [
            'title' => '添加用户',
        ], [
            'type' => 'primary',
        ]);
        $builder->addRightButton('edit', '修改', [
            'type' => 'modal',
            'api' => xbUrl('User/edit'),
            'path' => xbUrl('User/edit'),
        ], [
            'title' => '修改用户',
        ], [
            'type' => 'primary',
            'icon' => 'Edit',
        ]);
        $builder->addRightButton('del', '删除', [
            'type' => 'confirm',
            'api' => xbUrl('User/del'),
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
            'placeholder' => '用户账号/用户昵称',
        ]);
        $builder->addColumn('id', '序号', [
            'width' => 100,
        ]);
        $builder->addColumn('create_at', '注册时间', [
            'width' => 160,
        ]);
        $builder->addColumnEle('avatar', '用户头像', [
            'width' => 90,
            'params' => [
                'type' => 'image'
            ],
        ]);
        $builder->addColumn('username', '用户账号', [
        ]);
        $builder->addColumn('nickname', '用户昵称', [
        ]);
        $builder->addColumnEle('state', '用户状态', [
            'width' => 100,
            'params' => [
                'type' => 'switch',
                'api' => xbUrl('User/rowEdit'),
                'unchecked' => DictProvider::get('banText')->switch('10'),
                'checked' => DictProvider::get('banText')->switch('20'),
            ],
        ]);
        $data = $builder->create();
        return $this->successRes($data);
    }

    /**
     * 用户列表
     * @Apidoc\Method ("GET")
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        $keyword = $request->get('keyword');
        $where   = [];
        if ($keyword) {
            $where[] = ['username|nickname', 'like', "%{$keyword}%"];
        }
        $model = $this->model;
        $data  = $model
            ->where($where)
            ->order('id desc')
            ->paginate()
            ->toArray();
        return $this->successRes($data);
    }

    /**
     * 添加用户
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function add(Request $request)
    {
        if ($request->method() == 'POST') {
            $post = $request->post();
            Event::dispatch('common.event.UserEvent.add', $post);
            // 返回结果
            return $this->success('保存成功');
        }
        $view = $this->formView()->setMethod('POST')->create();
        return $this->successRes($view);
    }

    /**
     * 修改用户
     * @Apidoc\Method ("GET,PUT")
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function edit(Request $request)
    {
        $id = $request->get('id');
        if ($request->method() == 'PUT') {
            $post       = $request->post();
            $post['id'] = $id;
            Event::dispatch('common.event.UserEvent.edit', $post);
            // 返回结果
            return $this->success('保存成功');
        }
        $model = $this->model;
        $model = $model->where('id', $id)->find();
        if (!$model) {
            return $this->fail('数据不存在');
        }
        $view = $this->formView()
            ->setMethod('PUT')
            ->setData($model)
            ->create();
        return $this->successRes($view);
    }

    /**
     * 删除用户
     * @Apidoc\Method ("GET")
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function del(Request $request)
    {
        $id = $request->post('id');
        Event::dispatch('common.event.UserEvent.del', ['id' => $id]);
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
        $builder->addRow('username', 'input', '用户账号', '', [
            'col' => 12,
        ]);
        $builder->addRow('password', 'password', '登录密码', '', [
            'col' => 12,
        ]);
        $builder->addRow('nickname', 'input', '用户昵称', '', [
            'col' => 12,
        ]);
        $builder->addRow('avatar', 'uploadify', '用户头像', '', [
            'col' => 12,
        ]);
        return $builder;
    }
}
