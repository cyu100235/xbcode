<?php
namespace app\backend\controller;

use support\Request;
use app\model\WebRole;
use app\model\WebAdmin;
use app\validate\WebAdminValidate;
use xbcode\builder\FormBuilder;
use xbcode\builder\ListBuilder;
use xbcode\builder\table\attrs\RowEditTrait;
use xbcode\XbController;

/**
 * 站点管理员
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class WebAdminController extends XbController
{
    // 表格行编辑
    use RowEditTrait;

    /**
     * 初始化
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function init()
    {
        parent::init();
        $this->model = new WebAdmin;
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
        $saasAppid = $request->get('site_id');
        // 表格渲染
        $builder = new ListBuilder;
        $builder->addActionOptions('操作', [
            'width' => 180
        ]);
        $builder->pageConfig();
        $builder->addTopButton('add', '添加', [
            'type' => 'modal',
            'api' => xbUrl('WebAdmin/add'),
            'path' => xbUrl('WebAdmin/add'),
            'queryParams' => [
                'site_id' => $saasAppid,
            ],
        ], [
            'title' => '添加站点管理员',
            'customStyle' => [
                'width' => '500px',
                'height' => '60vh',
            ],
        ], [
            'type' => 'primary',
            'icon' => 'Plus'
        ]);
        $builder->addRightButton('edit', '修改', [
            'type' => 'modal',
            'api' => xbUrl('WebAdmin/edit'),
            'path' => xbUrl('WebAdmin/edit'),
        ], [
            'title' => '修改站点管理员',
            'customStyle' => [
                'width' => '500px',
                'height' => '60vh',
            ],
        ], [
            'type' => 'primary',
            'icon' => 'EditOutlined'
        ]);
        $builder->addRightButton('del', '删除', [
            'type' => 'confirm',
            'api' => xbUrl('WebAdmin/del'),
            'path' => xbUrl('WebAdmin/del'),
            'method' => 'DELETE',
        ], [
            'type' => 'error',
            'title' => '温馨提示',
            'content' => '是否确认删除该数据？',
        ], [
            'type' => 'danger',
            'icon' => 'DeleteOutlined'
        ]);
        $builder->addColumn('id', '序号', [
            'width' => 80,
        ]);
        $builder->addColumn('avatar', '用户头像', [
            'minWidth' => 180,
            'params' => [
                'type' => 'image',
            ]
        ]);
        $builder->addColumn('username', '登录账号', [
            'minWidth' => 180,
        ]);
        $builder->addColumn('nickname', '用户昵称', [
            'minWidth' => 180,
        ]);
        $builder->addColumn('role.title', '所属角色', [
            'minWidth' => 180,
        ]);
        $builder->addColumn('login_ip', '登录IP', [
            'minWidth' => 150,
        ]);
        $builder->addColumn('login_time', '登录时间', [
            'minWidth' => 150,
        ]);
        $builder->addColumn('is_system', '账号类型', [
            'minWidth' => 150,
            'params' => [
                'type' => 'dict',
                'props' => [
                    'options' => [
                        '10' => '普通管理员',
                        '20' => '超级管理员',
                    ]
                ],
            ]
        ]);
        $builder->addColumn('state', '账号状态', [
            'width' => 120,
            'params' => [
                'type' => 'switch',
                'api' => xbUrl('WebAdmin/rowEdit'),
                'props' => [
                    'activeText' => '正常',
                    'inactiveText' => '封禁',
                    'activeValue' => '20',
                    'inactiveValue' => '10',
                ],
            ]
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
        $saasAppid = $request->get('site_id');
        $data    = WebAdmin::with(['role'])
            ->where('saas_appid', $saasAppid)
            ->order('id desc')
            ->paginate()
            ->each(function ($item) {
                $item->login_ip = empty($item->login_ip) ? '未登录' : $item->login_ip;
                $item->login_time = empty($item->login_time) ? '未登录' : $item->login_time;
            });
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
        $saasAppid = $request->get('site_id');
        if (empty($saasAppid)) {
            return $this->fail('站点参数错误');
        }
        if ($request->method() == 'POST') {
            $post             = $request->post();
            $post['saas_appid'] = $saasAppid;
            $post['is_system']  = '10';

            // 数据验证
            xbValidate(WebAdminValidate::class, $post, 'add');

            $model = new WebAdmin;
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
        $model = WebAdmin::where('id', $id)->find();
        if (!$model) {
            return $this->fail('该数据不存在');
        }
        if ($request->method() == 'PUT') {
            $post = $request->post();

            // 数据验证
            xbValidate(WebAdminValidate::class, $post, 'edit');

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
        $id    = $request->post('id');
        $model = WebAdmin::where('id', $id)->find();
        if (!$model) {
            return $this->fail('该数据不存在');
        }
        if ($model->is_system == '20') {
            return $this->fail('系统管理员不可删除');
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
        $builder->setPosition('left');
        $saasAppid = request()->get('site_id');
        if ($saasAppid) {
            $where = [
                'saas_appid' => $saasAppid,
            ];
            $roles = WebRole::where($where)->column('title as label,id as value');
            $builder->addRow('role_id', 'select', '选择角色', '', [
                'prompt' => '请选择角色',
                'options' => $roles,
            ]);
        }
        $builder->addRow('username', 'input', '登录账号', '', [
            'prompt' => '字母数字下划线组合，5-20位',
        ]);
        $builder->addRow('password', 'input', '登录密码', '', [
            'prompt' => '字母数字下划线组合，5-20位',
        ]);
        $builder->addRow('nickname', 'input', '用户昵称', '', [
            'prompt' => '用户昵称，2-20位',
        ]);
        $builder->addRow('avatar', 'xbUpload', '用户头像', '', [
            'prompt' => '请上传用户头像',
            'props' => [
                'type' => 'image',
            ],
        ]);
        return $builder;
    }
}
