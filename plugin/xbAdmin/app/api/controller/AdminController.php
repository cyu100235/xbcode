<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbAdmin\app\api\controller;

use support\Request;
use support\Response;
use plugin\xbAdmin\enum\YesEnum;
use plugin\xbAdmin\enum\StateEnum;
use plugin\xbAdmin\app\model\Admin;
use plugin\xbAdmin\utils\PasswdUtil;
use plugin\xbAdmin\app\BaseController;
use plugin\xbAdmin\app\model\AdminRole;
use plugin\xbAdmin\app\validate\AdminValidate;
use plugin\xbAdmin\exception\business\ExceptionBusiness;

/**
 * 管理员账号接口
 * @copyright 贵州云铺网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class AdminController extends BaseController
{
    /**
     * 免权限校验
     * @var array
     */
    protected $noAuth = [
        'profile',
    ];

    /**
     * 列表
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
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

    /**
     * 添加
     * @param Request $request
     * @return Response
     */
    public function add(Request $request)
    {
        $post = (array) $request->post();
        xbValidate(AdminValidate::class, $post, 'add');
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

    /**
     * 修改管理员
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request)
    {
        $model = $this->findModel((int) $request->get('id'));
        $post = (array) $request->post();
        if ((string) $model['is_system'] === YesEnum::YES['value'] && !$this->isSystem()) {
            throw new ExceptionBusiness('系统内置管理员，禁止修改');
        }
        xbValidate(AdminValidate::class, $post, 'edit');
        if (isset($post['role_id'])) {
            $this->checkRoleAccess((int) $post['role_id']);
        }
        // 超级管理员不允许把自己降级，避免后台失去唯一入口
        if ((int) $model['id'] === $this->adminId()) {
            unset($post['role_id']);
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
     * 删除
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

    /**
     * 获取角色选项
     * @return Response
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function roleOptions()
    {
        $query = AdminRole::order('sort asc,id asc');
        $this->dataScope($query);
        $list = $query->column('title as label,id as value');
        $list = array_map(function ($item) {
            $item['value'] = (int) $item['value'];
            return $item;
        }, $list ?: []);
        return $this->successRes($list);
    }

    /**
     * 查询数据
     * @return Response
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function find()
    {
        $id = request()->get('id');
        $model = $this->findModel($id);
        return $this->successRes($model);
    }
    
    /**
     * 按照数据范围查找管理员
     * @param mixed $id
     * @throws ExceptionBusiness
     * @return Admin
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function findModel(?string $id = null): Admin
    {
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
    public function checkUsernameUnique(string $username, int $exceptId = 0): void
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
