<?php

namespace app\common\manager;

use app\common\builder\FormBuilder;
use app\common\builder\ListBuilder;
use app\common\enum\StatusEnum;
use app\common\enum\StatusEnumStyle;
use app\common\model\AdminRole;
use app\common\validate\AdminValidate;
use think\Request;
use app\common\model\Admin;

trait AdminMgr
{
    /**
     * 表格列
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function indexTable(Request $request)
    {
        $builder = new ListBuilder;
        $data    = $builder
            ->addActionOptions('操作', [
                'width' => 180
            ])
            ->pageConfig()
            ->addTopButton('add', '添加', [
                'type' => 'modal',
                'api' => '/Admin/add',
                'path' => '/Admin/add',
            ], [
                'title' => '添加管理员',
            ], [
                'type' => 'primary'
            ])
            ->addRightButton('edit', '修改', [
                'type' => 'modal',
                'api' => '/Admin/edit',
                'path' => '/Admin/edit',
            ], [
                'title' => '修改管理员信息',
            ], [
                'type' => 'primary',
            ])
            ->addRightButton('del', '删除', [
                'type' => 'confirm',
                'api' => '/Admin/del',
                'method' => 'delete',
            ], [
                'type' => 'error',
                'title' => '温馨提示',
                'content' => '是否确认删除该数据',
            ], [
                'type' => 'danger',
            ])
            ->addColumn('username', '登录账号')
            ->addColumn('nickname', '用户昵称')
            ->addColumnEle('headimg', '用户头像', [
                'params' => [
                    'type' => 'image',
                ],
            ])
            ->addColumn('role.title', '所属角色')
            ->addColumn('login_ip', '最近登录IP')
            ->addColumn('login_time', '最近登录时间')
            ->addColumnEle('status', '当前状态', [
                'width' => 90,
                'params' => [
                    'type' => 'tags',
                    'options' => StatusEnum::dict(),
                    'style' => StatusEnumStyle::labelMap('type', false),
                ],
            ])
            ->create();
        return $this->successRes($data);
    }

    /**
     * 列表数据
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        $admin_id = $request->userId;
        $where    = [
            'pid'   => $admin_id,
        ];
        $data = Admin::with(['role'])
            ->where($where)
            ->paginate()
            ->toArray();
        return $this->successRes($data);
    }

    /**
     * 添加
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function add(Request $request)
    {
        $admin_id = $request->user['id'];
        if ($request->method() == 'POST') {
            $post = $request->post();
            $post['pid']    = $admin_id;

            # 数据验证
            xbValidate(AdminValidate::class, $post, 'add');
            
            # 验证是否已存在
            $where = [
                'username'      => $post['username']
            ];
            if (Admin::where($where)->count()) {
                return $this->fail('该登录账号已存在');
            }
            $model = new Admin;
            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            return $this->success('保存成功');
        }
        $view = $this->formView()
        ->setMethod('POST')
        ->create();
        return $this->successRes($view);
    }

    /**
     * 修改
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function edit(Request $request)
    {
        $admin_id = $request->userId;
        $id = $request->get('id');
        $where    = [
            'id'    => $id,
            'pid'   => $admin_id,
        ];
        $model = Admin::where($where)->find();
        if (!$model) {
            return $this->fail('该数据不存在');
        }
        if ($request->method() == 'PUT') {
            $post = $request->post();

            // 数据验证
            xbValidate(AdminValidate::class, $post, 'edit');

            // 空密码，不修改
            if (empty($post['password'])) {
                unset($post['password']);
            }
            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
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
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function del(Request $request)
    {
        $id = $request->post('id');
        if (!Admin::where('id',$id)->delete()) {
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
        $adminId = request()->userId;
        $builder = new FormBuilder;
        $builder->addRow('role_id', 'select', '所属角色', '', [
            'col'           => 12,
            'options'       => AdminRole::selectOptions((int)$adminId)
        ]);
        $builder->addRow('status', 'radio', '用户状态', '10', [
            'col'           => 12,
            'options'       => StatusEnum::options()
        ]);
        $builder->addRow('username', 'input', '登录账号', '', [
            'col'           => 12,
        ]);
        $builder->addRow('password', 'input', '登录密码', '', [
            'col'           => 12,
            'placeholder'   => '不填写，则不修改密码',
        ]);
        $builder->addRow('nickname', 'input', '用户昵称', '', [
            'col'           => 12,
        ]);
        $builder->addRow('avatar', 'uploadify', '用户头像', '', [
            'col'           => 12,
            'props'         => [
                'format'    => ['jpg', 'png', 'gif']
            ],
        ]);
        return $builder;
    }
}
