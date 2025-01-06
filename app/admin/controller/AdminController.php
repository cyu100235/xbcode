<?php
namespace app\admin\controller;

use support\Request;
use app\model\WebRole;
use app\model\WebAdmin;
use xbcode\XbController;
use Tinywan\Jwt\JwtToken;
use xbcode\utils\PasswdUtil;
use xbcode\builder\FormBuilder;
use xbcode\builder\ListBuilder;
use app\validate\WebAdminValidate;
use xbcode\builder\table\attrs\RowEditTrait;

/**
 * 管理员管理
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class AdminController extends XbController
{
    // 表格行编辑
    use RowEditTrait;

    /**
     * 模型
     * @var WebAdmin
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
        // 表格渲染
        $builder = new ListBuilder;
        $builder->addActionOptions('操作', [
            'width' => 180
        ]);
        $builder->pageConfig();
        $builder->addTopButton('add', '添加', [
            'type' => 'modal',
            'api' => xbUrl('Admin/add'),
            'path' => xbUrl('Admin/add'),
        ], [
            'title' => '添加管理员',
            'customStyle' => [
                'width' => '500px',
                'height' => '60vh',
            ],
        ], [
            'type' => 'primary'
        ]);
        $builder->addRightButton('edit', '修改', [
            'type' => 'modal',
            'api' => xbUrl('Admin/edit'),
            'path' => xbUrl('Admin/edit'),
        ], [
            'title' => '修改管理员',
            'customStyle' => [
                'width' => '500px',
                'height' => '60vh',
            ],
        ], [
            'type' => 'primary',
            'icon' => 'Edit'
        ]);
        $builder->addRightButton('del', '删除', [
            'type' => 'confirm',
            'api' => xbUrl('Admin/del'),
            'path' => xbUrl('Admin/del'),
            'method' => 'delete',
        ], [
            'type' => 'error',
            'title' => '温馨提示',
            'content' => '是否确认删除该数据？',
        ], [
            'type' => 'danger',
            'icon' => 'Delete'
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
        $builder->addColumn('state', '账号状态', [
            'width' => 120,
            'params' => [
                'type' => 'switch',
                'api' => xbUrl('Admin/rowEdit'),
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
        $adminId = JwtToken::getCurrentId();
        $model   = $this->model;
        $data    = $model->with(['role'])
            ->where('admin_id', $adminId)
            ->order('id desc')
            ->paginate();
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
            $adminId          = JwtToken::getCurrentId();
            $post             = $request->post();
            $post['admin_id'] = $adminId;

            // 数据验证
            xbValidate(WebAdminValidate::class, $post, 'add');

            $model = $this->model;
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
        $model = $this->model;
        $model = $model->where('id', $id)->find();
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
        $formData = $model->toArray();
        unset($formData['password']);
        $builder = $this->formView();
        $builder->setMethod('PUT');
        $builder->setFormData($formData);
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
            return $this->fail('该数据不存在');
        }
        // 检测是否系统管理员
        if ($model['is_system'] == '20') {
            return $this->fail('无法删除系统管理员');
        }
        // 执行删除
        if (!$model->delete()) {
            return $this->fail('删除失败');
        }
        return $this->success('删除成功');
    }
    
    /**
     * 修改个人资料
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function profile(Request $request)
    {
        $adminId = JwtToken::getCurrentId();
        $model = $this->model;
        $model = $model->where('id', $adminId)->find();
        if (!$model) {
            return $this->fail('该数据不存在');
        }
        if ($request->method() === 'PUT') {
            $post = $request->post();
            // 数据验证
            xbValidate(WebAdminValidate::class, $post, 'profile');
            // 原登录密码与旧密码一致
            if ($post['originpwd'] === $post['newpassword']) {
                return $this->fail('新密码不能与原密码一致');
            }
            // 验证登录密码
            $password = PasswdUtil::create($post['originpwd']);
            $originPwd = (string) $model['password'];
            if ($password !== $originPwd) {
                return $this->fail('原登录密码错误');
            }
            if (!$model->save(['password'=> $post['newpassword']])) {
                return $this->fail('个人资料修改失败');
            }
            return $this->success('个人资料修改成功');
        }
        $formData = $model->toArray();
        unset($formData['password']);
        $builder = new FormBuilder;
        $builder->setMethod('PUT');
        $builder->setPosition('left',100);
        $builder->addRow('username', 'input', '登录账号', '', [
            'prompt' => '字母数字下划线组合，4-20位',
        ]);
        $builder->addRow('originpwd', 'input', '旧登录密码', '', [
            'prompt' => '请输入原登录密码',
        ]);
        $builder->addRow('newpassword', 'input', '新登录密码', '', [
            'prompt' => '请再次输入登录密码',
        ]);
        $builder->addRow('nickname', 'input', '用户昵称', '', [
            'prompt' => '用户昵称，2-20位',
        ]);
        $builder->addRow('avatar', 'xbUpload', '用户头像', '', [
            'prompt' => '请上传用户头像',
            'props' => [
                'type' => 'image',
                'isUpload' => true,
            ],
        ]);
        $builder->setFormData($formData);
        $data = $builder->create();
        return $this->successRes($data);
    }

    /**
     * 表单
     * @return FormBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function formView()
    {
        $adminId = JwtToken::getCurrentId();
        $builder = new FormBuilder;
        $builder->setPosition('left');

        // 所属角色
        $roles = WebRole::where('admin_id', $adminId)
            ->order('sort asc,id asc')
            ->column('title as label,id as value');
        $builder->addRow('role_id', 'select', '所属角色', '', [
            'prompt' => '请选择所属角色',
            'options' => $roles,
        ]);
        $builder->addRow('username', 'input', '登录账号', '', [
            'prompt' => '字母数字下划线组合，4-20位',
        ]);
        $builder->addRow('password', 'input', '登录密码', '', [
            'prompt' => '字母数字下划线组合，6-20位',
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
