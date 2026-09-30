<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbAdmin\app\admin\controller;

use support\Request;
use support\Response;
use plugin\xbAdmin\api\Url;
use plugin\xbCode\api\Gateway;
use plugin\xbAdmin\enum\YesEnum;
use plugin\xbAdmin\enum\StateEnum;
use plugin\xbAdmin\app\BaseController;
use plugin\xbCode\builder\Renders\XbForm;
use plugin\xbCode\builder\Renders\XbCrud;

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
            $builder = XbCrud::make();
            $builder->useCRUD()->quickSaveItemApi(Url::make('rowEdit')->get());
            $builder->addHeaderDialog('添加用户', Url::make('add'))
                ->primary()->title('添加管理员用户');
            $builder->addFilterInput('username', '登录账号');
            $builder->addFilterInput('nickname', '用户昵称');
            $builder->addFilterSelect('state', '账号状态')->options(StateEnum::options())->clearable(true);
            $builder->addColumn('id', '序号')->width(80)->align('center');
            $builder->addColumn('username', '登录账号');
            $builder->addColumn('nickname', '用户昵称')->width(140);
            $builder->addColumn('role.title', '所属角色')->width(160);
            $builder->addColumnSwitchApi('state', '账号状态', Url::make('rowEdit')->get(), StateEnum::switch());
            $builder->addColumnMap('is_system', '系统账号', YesEnum::dict())->width(110);
            $builder->addColumn('login_ip', '登录IP');
            $builder->addColumnDateTime('login_time', '最后登录')->width(165);
            $builder->addColumnDateTime('create_at', '创建时间')->width(165);
            $builder->setActionConfig('width', 150);
            $builder->addRightActionDialog('修改', Url::make('edit'))
                ->disabledOn('this.is_system == 20 && this.id != ' . $this->adminId())->title('修改管理员用户')->disabledTip('系统内置管理员，禁止修改');
            $builder->addRightActionConfirm('删除', Url::make('del'))
                ->disabledOn('this.is_system == 20')
                ->disabledTip('系统内置管理员，禁止删除');
            return $this->successRes($builder);
        }
        $query = request()->get();
        $result = Gateway::get('xbAdmin/api/Admin/index', $query);
        return $this->response($result);
    }

    /**
     * 添加管理员
     * @param Request $request
     * @return Response
     */
    public function add(Request $request)
    {
        if ($request->method() === 'POST') {
            $post = $request->post();
            $result = Gateway::post('xbAdmin/api/Admin/add', $post);
            return $this->response($result);
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
        $id = $request->get('id');
        $formData = $this->findModel((int) $id);
        if ($request->method() === 'PUT') {
            $post = $request->post();
            $result = Gateway::put("xbAdmin/api/Admin/edit?id={$id}", $post);
            return $this->response($result);
        }
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
        $post = $request->post();
        $result = Gateway::put('xbAdmin/api/Admin/rowEdit', $post);
        return $this->response($result);
    }

    /**
     * 删除管理员
     * @param Request $request
     * @return Response
     */
    public function del(Request $request)
    {
        $id = (int) $request->get('id');
        $result = Gateway::delete("xbAdmin/api/Admin/del?id={$id}");
        return $this->response($result);
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
            $post = $request->post();
            $result = Gateway::put("xbAdmin/api/Admin/profile", $post);
            return $this->response($result);
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
        $builder->addRowSelect('role_id', '所属角色')
            ->options($roles)
            ->required(true);
        $builder->addRowInput('username', '登录账号')
            ->required(true)
            ->description('2-20位，登录时使用');
        $builder->addRowInput('password', '登录密码')
            ->password()
            ->description('5-20位，修改时留空表示不变更');
        $builder->addRowInput('nickname', '用户昵称')
            ->required(true)
            ->description('2-20位');
        return $builder;
    }

    /**
     * 获取可选择角色选项
     * @return array
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function roleOptions(): array
    {
        return Gateway::get('xbAdmin/api/Admin/roleOptions')['data'] ?? [];
    }

    /**
     * 按数据范围查找管理员
     * @param int $id
     * @return array
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function findModel(int $id): array
    {
        $result = Gateway::get("xbAdmin/api/Admin/find?id={$id}");
        $model = $result['data'] ?? [];
        return $model;
    }
}
