<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\app\admin\controller;

use support\Request;
use support\Response;
use plugin\xbAdmin\api\Url;
use plugin\xbAdmin\app\model\Admin;
use plugin\xbAdmin\enum\StateEnum;
use plugin\xbAdmin\enum\YesEnum;
use plugin\xbAdmin\utils\PasswdUtil;
use plugin\xbAdmin\app\model\AdminRole;
use plugin\xbCode\builder\Renders\XbForm;
use plugin\xbCode\builder\Renders\XbCrud;
use plugin\xbAdmin\app\validate\AdminValidate;
use plugin\xbAdmin\exception\business\ExceptionBusiness;

/**
 * 管理员账号管理
 */
class AdminController extends BaseController
{
    /**
     * 免权限校验方法
     *
     * 个人资料是内置用户菜单的固定入口，任何登录用户都应可访问，
     * 否则普通管理员会被自己的权限配置锁死。
     * @var array
     */
    protected $noAuth = [
        'profile',
    ];

    /**
     * 账号列表
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        if ($request->get('_act')) {
            $query = Admin::with(['role'])->order('id desc');
            $this->dataScope($query);
            if (($username = trim((string) $request->get('username', ''))) !== '') {
                $query->where('username', 'like', "%{$username}%");
            }
            if (($nickname = trim((string) $request->get('nickname', ''))) !== '') {
                $query->where('nickname', 'like', "%{$nickname}%");
            }
            if (in_array((string) $request->get('state', ''), array_column(StateEnum::toArray(), 'value'), true)) {
                $query->where('state', (string) $request->get('state'));
            }
            return $this->successData($query->paginate());
        }
        $builder = XbCrud::make();
        $builder->useCRUD()->quickSaveItemApi(Url::make('rowEdit')->get());
        $builder->addHeaderDialog('添加用户', Url::make('Admin/add'), [
            'title' => '添加管理员用户',
            'size' => 'md',
        ])->level('primary');
        $builder->addFilterInput('username', '登录账号');
        $builder->addFilterInput('nickname', '用户昵称');
        $builder->addFilterSelect('state', '账号状态')->options(StateEnum::options())->clearable(true);
        $builder->addColumn('id', '序号')->width(80);
        $builder->addColumn('username', '登录账号')->minWidth(140);
        $builder->addColumn('nickname', '用户昵称')->minWidth(140);
        $builder->addColumn('role.title', '所属角色')->minWidth(160);
        $builder->addColumnSwitchApi(
            'state',
            '账号状态',
            Url::make('rowEdit')->get(),
            [
                'onText' => StateEnum::ENABLED['label'],
                'offText' => StateEnum::DISABLED['label'],
                'trueValue' => StateEnum::ENABLED['value'],
                'falseValue' => StateEnum::DISABLED['value'],
            ]
        );
        $builder->addColumnMap('is_system', '系统账号', YesEnum::dict('label'))->width(110);
        $builder->addColumn('login_ip', '登录IP')->minWidth(140);
        $builder->addColumnDateTime('login_time', '最后登录')->width(165);
        $builder->addColumnDateTime('create_at', '创建时间')->width(165);
        $builder->setActionConfig('width', 130);
        $builder->addRightActionDialog('修改', Url::make('edit'), [
            'title' => '修改管理员用户',
            'size' => 'md',
        ])->disabledOn('this.is_system == 20 && this.id != ' . $this->adminId())
            ->disabledTip('系统内置管理员，禁止修改');
        $builder->addRightActionConfirm('删除', Url::make('del'))
            ->disabledOn('this.is_system == 20')
            ->disabledTip('系统内置管理员，禁止删除');
        return $this->successRes($builder);
    }

    /**
     * 添加管理员
     * @param Request $request
     * @return Response
     */
    public function add(Request $request)
    {
        if ($request->method() === 'POST') {
            $post = (array) $request->post();
            xbAdminValidate(AdminValidate::class, $post, 'add');
            $this->checkRoleAccess((int) $post['role_id']);
            $this->checkUsernameUnique((string) $post['username']);
            $post['admin_id'] = $this->adminId();
            $post['is_system'] = YesEnum::NO['value'];
            $model = new Admin;
            if (!$model->save($post)) {
                throw new ExceptionBusiness('保存失败');
            }
            return $this->success('保存成功');
        }
        $builder = $this->formView();
        $builder->setSaveMethod('POST');
        return $this->successRes($builder);
    }

    /**
     * 修改管理员
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request)
    {
        $model = $this->findModel((int) $request->get('id'));
        if ($request->method() === 'PUT') {
            $post = (array) $request->post();
            if ((string) $model['is_system'] === YesEnum::YES['value'] && !$this->isSystem()) {
                throw new ExceptionBusiness('系统内置管理员，禁止修改');
            }
            // 超级管理员不允许把自己降级，避免后台失去唯一入口
            if ((int) $model['id'] === $this->adminId()) {
                unset($post['role_id']);
            }
            xbAdminValidate(AdminValidate::class, $post, 'edit');
            if (isset($post['role_id'])) {
                $this->checkRoleAccess((int) $post['role_id']);
            }
            $this->checkUsernameUnique((string) $post['username'], (int) $model['id']);
            if (empty($post['password'])) {
                unset($post['password']);
            }
            unset($post['is_system'], $post['admin_id']);
            if (!$model->save($post)) {
                throw new ExceptionBusiness('保存失败');
            }
            return $this->success('保存成功');
        }
        $formData = $model->toArray();
        $formData['password'] = '';
        $builder = $this->formView();
        $builder->setSaveMethod('PUT');
        $builder->setData($formData);
        return $this->successRes($builder);
    }

    /**
     * 快速编辑列表字段
     * @param Request $request
     * @return Response
     */
    public function rowEdit(Request $request)
    {
        $post = (array) $request->post();
        $model = $this->findModel((int) ($post['id'] ?? 0));
        if ((string) $model['is_system'] === YesEnum::YES['value']) {
            throw new ExceptionBusiness('系统内置管理员，禁止修改');
        }
        $allow = ['state'];
        $data = [];
        foreach ($allow as $field) {
            if (array_key_exists($field, $post)) {
                $data[$field] = $post[$field];
            }
        }
        if (empty($data)) {
            throw new ExceptionBusiness('没有需要保存的数据');
        }
        if (isset($data['state']) && (int) $model['id'] === $this->adminId()) {
            throw new ExceptionBusiness('不能修改自己的账号状态');
        }
        if (!$model->save($data)) {
            throw new ExceptionBusiness('保存失败');
        }
        return $this->success('保存成功');
    }

    /**
     * 删除管理员
     * @param Request $request
     * @return Response
     */
    public function del(Request $request)
    {
        $model = $this->findModel((int) $request->get('id'));
        if ((string) $model['is_system'] === YesEnum::YES['value']) {
            throw new ExceptionBusiness('系统内置管理员，禁止删除');
        }
        if ((int) $model['id'] === $this->adminId()) {
            throw new ExceptionBusiness('不能删除当前登录账号');
        }
        if (!$model->delete()) {
            throw new ExceptionBusiness('删除失败');
        }
        return $this->success('删除成功');
    }

    /**
     * 个人资料
     * @param Request $request
     * @return Response
     */
    public function profile(Request $request)
    {
        $model = $this->currentModel();
        if ($request->method() === 'PUT') {
            $post = (array) $request->post();
            $nickname = trim((string) ($post['nickname'] ?? ''));
            if (mb_strlen($nickname) < 2 || mb_strlen($nickname) > 20) {
                throw new ExceptionBusiness('用户昵称长度需为2-20位');
            }
            $data = ['nickname' => $nickname];
            $origin = (string) ($post['originpwd'] ?? '');
            $newPassword = (string) ($post['newpassword'] ?? '');
            if ($origin !== '' || $newPassword !== '') {
                if ($origin === '' || $newPassword === '') {
                    throw new ExceptionBusiness('原登录密码与新登录密码均需填写');
                }
                if ($origin === $newPassword) {
                    throw new ExceptionBusiness('新密码不能与原密码一致');
                }
                if (mb_strlen($newPassword) < 5 || mb_strlen($newPassword) > 20) {
                    throw new ExceptionBusiness('新登录密码长度需为5-20位');
                }
                if (!PasswdUtil::check($origin, (string) $model['password'])) {
                    throw new ExceptionBusiness('原登录密码错误');
                }
                $data['password'] = $newPassword;
            }
            if (!$model->save($data)) {
                throw new ExceptionBusiness('个人资料修改失败');
            }
            return $this->success('个人资料修改成功');
        }
        $formData = $model->toArray();
        unset($formData['password']);
        $builder = XbForm::make();
        $builder->useForm()->wrapWithPanel(false);
        $builder->addRowStatic('username', '登录账号', $formData['username'] ?? '');
        $builder->addRowInput('nickname', '用户昵称')->description('2-20位');
        $builder->addRowInput('originpwd', '原登录密码')->password()->description('不修改密码请留空');
        $builder->addRowInput('newpassword', '新登录密码')->password()->description('不修改密码请留空');
        $builder->setSaveMethod('PUT');
        $builder->setData($formData);
        return $this->successRes($builder);
    }

    /**
     * 表单视图
     * @return XbForm
     */
    protected function formView(): XbForm
    {
        $roles = $this->roleOptions();
        $builder = XbForm::make();
        $builder->addRowSelect('role_id', '所属角色')->options($roles)->required(true);
        $builder->addRowInput('username', '登录账号')->required(true)->description('2-20位，登录时使用');
        $builder->addRowInput('password', '登录密码')->password()->description('5-20位，修改时留空表示不变更');
        $builder->addRowInput('nickname', '用户昵称')->required(true)->description('2-20位');
        return $builder;
    }

    /**
     * 可选择的角色选项
     *
     * 非超级管理员只能把用户挂到自己名下的角色，避免出现越权提权。
     * @return array
     */
    protected function roleOptions(): array
    {
        $query = AdminRole::order('sort asc,id asc');
        $this->dataScope($query);
        $list = $query->column('title as label,id as value');
        return array_map(function ($item) {
            $item['value'] = (int) $item['value'];
            return $item;
        }, $list ?: []);
    }

    /**
     * 按数据范围查找管理员
     * @param int $id
     * @throws ExceptionBusiness
     * @return Admin
     */
    protected function findModel(int $id): Admin
    {
        if ($id <= 0) {
            throw new ExceptionBusiness('数据不存在');
        }
        $query = Admin::where('id', $id);
        $model = $this->dataScope($query)->find();
        if (!$model) {
            throw new ExceptionBusiness('该数据不存在');
        }
        return $model;
    }

    /**
     * 校验角色归属
     * @param int $roleId
     * @throws ExceptionBusiness
     * @return void
     */
    protected function checkRoleAccess(int $roleId): void
    {
        $query = AdminRole::where('id', $roleId);
        if (!$this->dataScope($query)->find()) {
            throw new ExceptionBusiness('所属角色不存在或无权使用');
        }
    }

    /**
     * 校验登录账号唯一
     * @param string $username
     * @param int $exceptId 排除的ID
     * @throws ExceptionBusiness
     * @return void
     */
    protected function checkUsernameUnique(string $username, int $exceptId = 0): void
    {
        $query = Admin::where('username', $username);
        if ($exceptId > 0) {
            $query->where('id', '<>', $exceptId);
        }
        if ($query->count() > 0) {
            throw new ExceptionBusiness('登录账号已存在');
        }
    }
}
