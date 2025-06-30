<?php
namespace plugin\xbCode\app\admin\controller;

use support\Request;
use plugin\xbCode\XbController;
use plugin\xbCode\builder\Builder;
use plugin\xbCode\app\model\Admin;
use plugin\xbCode\utils\PasswdUtil;
use plugin\xbCode\app\model\AdminRole;
use plugin\xbCode\builder\Renders\Grid;
use plugin\xbCode\builder\Renders\Form;
use plugin\xbCode\builder\Components\Form\Group;
use plugin\xbCode\app\validate\AdminValidate;

/**
 * 管理员管理
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class AdminController extends XbController
{
    /**
     * 列表
     * @param \support\Request $request
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function index(Request $request)
    {
        $act = $request->get('_act');
        if ($act) {
            $adminId = $request->uid;
            $data = Admin::with(['role'])
                ->where('admin_id', $adminId)
                ->order('id desc')
                ->paginate();
            return $this->successData($data);
        }
        $builder = Builder::crud(function (Grid $builder) {
            $builder->useCRUD()->alwaysShowPagination(true);
            $builder->setCRUDActionConfig('width', 130);
            $builder->addHeaderDialogBtn('添加用户', xbUrl('Admin/add'), [
                'dialog' => [
                    'title' => '添加管理员用户',
                    'size' => 'md',
                ],
            ])->level('primary');

            $builder->addActionDialogBtn('修改', xbUrl('Admin/edit'), [
                'dialog' => [
                    'title' => '修改管理员用户',
                    'size' => 'md',
                ],
            ])->level('primary');
            $builder->addActionConfirmBtn('删除', xbUrl('Admin/del'))->level('danger');

            $builder->addColumn('id', '序号', [
                'width' => 80,
            ]);
            $builder->addColumnImage('avatar', '用户头像');
            $builder->addColumn('username', '登录账号');
            $builder->addColumn('nickname', '用户昵称');
            $builder->addColumn('role.title', '所属角色', [
                'minWidth' => 180,
            ]);
            $builder->addColumn('login_ip', '登录IP', [
                'minWidth' => 150,
            ]);
        });
        return $this->successRes($builder);
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
            $adminId = $request->uid;
            $post = $request->post();
            $post['admin_id'] = $adminId;

            // 数据验证
            xbValidate(AdminValidate::class, $post, 'add');

            $model = new Admin;
            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            return $this->success('保存成功');
        }
        $builder = $this->formView();
        $builder->setMethod('POST');
        return $this->successRes($builder);
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
        $id = $request->get('id');
        $model = Admin::where('id', $id)->find();
        if (!$model) {
            return $this->fail('该数据不存在');
        }
        if ($request->method() == 'PUT') {
            $post = $request->post();

            // 数据验证
            xbValidate(AdminValidate::class, $post, 'edit');

            if (empty($post['password'])) {
                unset($post['password']);
            }

            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            return $this->success('保存成功');
        }
        $formData = $model->toArray();
        $formData['password'] = '';
        $builder = $this->formView();
        $builder->setMethod('PUT');
        $builder->setData($formData);
        return $this->successRes($builder);
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
        $id = $request->get('id');
        $model = Admin::where('id', $id)->find();
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
        $adminId = $request->uid;
        $model = Admin::where('id', $adminId)->find();
        if (!$model) {
            return $this->fail('该数据不存在');
        }
        if ($request->method() === 'PUT') {
            $post = $request->post();
            $data = [
                'nickname' => $post['nickname'],
                'avatar' => $post['upload'] ?? $model['avatar'],
            ];
            // 是否存在修改密码
            if (!empty($post['originpwd'])) {
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
                $data['password'] = $post['newpassword'];
            }
            if (!$model->save($data)) {
                return $this->fail('个人资料修改失败');
            }
            return $this->success('个人资料修改成功');
        }
        $formData = $model->toArray();
        unset($formData['password']);
        $builder = Builder::form(function (Form $builder) {
            $builder->useForm()->wrapWithPanel(false);
            $builder->addRowInput('username', '登录账号')->disabled(true);
            $builder->addRowInput('originpwd', '原登录密码')->description('请填写原来登录密码')->password();
            $builder->addRowInput('newpassword', '新登录密码')->description('请填写新的登录密码')->password();
            $builder->addRowInput('nickname', '用户昵称')->description('用户昵称，2-10位');
            $builder->addRowUploadImage('upload', '用户头像')->description('建议尺寸:200 * 200 px');
        });
        $builder->setMethod('PUT');
        $builder->setData($formData);
        return $this->successRes($builder);
    }

    /**
     * 表单视图
     * @return Form
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function formView()
    {
        $adminId = request()->uid;
        $roles = AdminRole::where('admin_id', $adminId)
            ->order('sort asc,id asc')
            ->column('title as label,id as value');
        return Builder::form(function (Form $builder) use ($roles) {
            /** @var Group */
            $userGroup = Group::make()->className('flex-1');
            // Flex布局
            $builder->addRowFlex([
                $builder->addRowUploadImage('avatar',''),
                $userGroup->body([
                    $builder->addRowInput('username', '登录账号')->columnRatio(12),
                    $builder->addRowInput('password', '登录密码')->className('mt-1')->password(),
                ]),
            ])->justify('flex-start')->alignItems('start');
            // 用户信息分组
            $builder->addRowGroup([
                $builder->addRowSelect('role_id', '所属角色')->options($roles),
                $builder->addRowInput('nickname', '用户昵称'),
            ]);
        });
    }
}
