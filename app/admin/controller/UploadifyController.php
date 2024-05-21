<?php
namespace app\admin\controller;

use app\common\builder\FormBuilder;
use app\common\builder\ListBuilder;
use app\model\AdminRole;
use app\common\providers\UploadProvider;
use app\common\utils\enum\BanEnum;
use app\common\utils\enum\BanStyle;
use app\admin\validate\AdminValidate;
use hg\apidoc\annotation as Apidoc;
use Tinywan\Jwt\JwtToken;
use app\common\XbController;
use app\model\Admin;
use support\Request;

/**
 * 附件管理器
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class UploadifyController extends XbController
{
    /**
     * 管理员-表格
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function indexTable(Request $request)
    {
        $builder = new ListBuilder;
        $builder->addActionOptions('操作', [
            'width' => 180
        ]);
        $builder->pageConfig();
        $builder->addRightButton('edit', '修改', [
            'type' => 'modal',
            'api' => 'Uploadify/edit',
            'path' => 'Uploadify/edit',
        ], [
            'title' => '修改附件',
        ], [
            'type' => 'primary',
        ]);
        $builder->addRightButton('del', '删除', [
            'type' => 'confirm',
            'api' => 'Uploadify/del',
            'method' => 'delete',
        ], [
            'type' => 'error',
            'title' => '温馨提示',
            'content' => '是否确认删除该数据',
        ], [
            'type' => 'danger',
        ]);
        $builder->addColumn('username', '登录账号');
        $builder->addColumn('nickname', '用户昵称');
        $builder->addColumnEle('headimg', '用户头像', [
            'params' => [
                'type' => 'image',
            ],
        ]);
        $builder->addColumn('role.title', '所属角色');
        $builder->addColumn('login_ip', '最近登录IP');
        $builder->addColumn('login_time', '最近登录时间');
        $builder->addColumnEle('state', '当前状态', [
            'width' => 90,
            'params' => [
                'type' => 'tags',
                'options' => BanEnum::dict(),
                'style' => BanStyle::labelMap('type', false),
            ],
        ]);
        $data = $builder->create();
        return $this->successRes($data);
    }

    /**
     * 管理员列表
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        $adminId = JwtToken::getCurrentId();
        $data    = Admin::with(['role'])
            ->where('admin_id', $adminId)
            ->paginate()
            ->toArray();
        return $this->successRes($data);
    }

    /**
     * 添加管理员
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function add(Request $request)
    {
        $adminId = JwtToken::getCurrentId();
        if ($request->method() == 'POST') {
            $post             = $request->post();
            $post['admin_id'] = $adminId;

            // 数据验证
            xbValidate(AdminValidate::class, $post, 'add');

            // 验证是否已存在
            $where = [
                'username' => $post['username']
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
        $builder = $this->formView();
        $builder->setMethod('POST');
        $data = $builder->create();
        return $this->successRes($data);
    }

    /**
     * 修改管理员
     * @Apidoc\Method ("GET,PUT")
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function edit(Request $request)
    {
        $id    = $request->get('id');
        $model = Admin::where('id', $id)->find();
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
        $builder = $this->formView();
        $builder->setMethod('PUT');
        $builder->setData($model);
        $data = $builder->create();
        return $this->successRes($data);
    }

    /**
     * 删除管理员
     * @Apidoc\Method ("DELETE")
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function del(Request $request)
    {
        $id = $request->post('id');
        if (!Admin::where('id', $id)->delete()) {
            return $this->fail('删除失败');
        }
        return $this->success('删除成功');
    }

    /**
     * 修改管理员资料
     * @Apidoc\Method ("GET,PUT")
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function info(Request $request)
    {
        $adminId = JwtToken::getCurrentId();
        $model   = Admin::where('id', $adminId)->find();
        if (!$model) {
            return $this->fail('该数据不存在');
        }
        if ($request->method() == 'PUT') {
            $post = $request->post();

            // 数据验证
            xbValidate(AdminValidate::class, $post, 'editSelf');

            // 空密码，不修改
            if (empty($post['password'])) {
                unset($post['password']);
            }
            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            return $this->success('保存成功');
        }
        $data           = $model->toArray();
        $data['avatar'] = UploadProvider::url($data['avatar']);
        $builder        = new FormBuilder;
        $builder->addRow('username', 'input', '登录账号', '', [
            'col' => 12,
        ]);
        $builder->addRow('password', 'input', '登录密码', '', [
            'col' => 12,
            'placeholder' => '不填写，则不修改密码',
        ]);
        $builder->addRow('nickname', 'input', '用户昵称', '', [
            'col' => 12,
        ]);
        $builder->addRow('avatar', 'uploadify', '用户头像', '', [
            'col' => 12,
            'props' => [
                'format' => ['jpg', 'png', 'gif']
            ],
        ]);
        $builder->setMethod('PUT');
        $builder->setFormData($data);
        $view = $builder->create();
        return $this->successRes($view);
    }

    /**
     * 表单
     * @return FormBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function formView()
    {
        $adminId = (int) JwtToken::getCurrentId();
        $builder = new FormBuilder;
        $builder->addRow('role_id', 'select', '所属角色', '', [
            'col' => 12,
            'options' => AdminRole::getOptions((int) $adminId)
        ]);
        $builder->addRow('state', 'radio', '用户状态', '20', [
            'col' => 12,
            'options' => BanEnum::options()
        ]);
        $builder->addRow('username', 'input', '登录账号', '', [
            'col' => 12,
        ]);
        $builder->addRow('password', 'input', '登录密码', '', [
            'col' => 12,
            'placeholder' => '不填写，则不修改密码',
        ]);
        $builder->addRow('nickname', 'input', '用户昵称', '', [
            'col' => 12,
        ]);
        $builder->addRow('avatar', 'uploadify', '用户头像', '', [
            'col' => 12,
            'props' => [
                'format' => ['jpg', 'png', 'gif']
            ],
        ]);
        return $builder;
    }
}
