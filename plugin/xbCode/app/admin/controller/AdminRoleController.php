<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\app\admin\controller;

use support\Request;
use plugin\xbCode\api\Url;
use plugin\xbCode\app\model\Admin;
use plugin\xbCode\api\MenuChecked;
use plugin\xbCode\app\model\AdminRule;
use plugin\xbCode\app\model\AdminRole;
use plugin\xbCode\builder\Renders\XbForm;
use plugin\xbCode\builder\Renders\XbCrud;
use plugin\xbCode\app\validate\AdminRoleValidate;
use plugin\xbCode\builder\Components\Form\Transfer;

/**
 * 角色管理
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class AdminRoleController extends BaseController
{
    /**
     * 表格
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function index(Request $request)
    {
        $act = $request->get('_act');
        if ($act) {
            $adminId = $request->uid;
            $data = AdminRole::where('admin_id', $adminId)
                ->order('sort asc,id asc')
                ->paginate()
                ->each(function ($item) {
                    $where = [
                        'role_id' => $item['id'],
                    ];
                    $count = Admin::where($where)->count();
                    $item->num = $count;
                });
            return $this->successRes($data);
        }
        $builder = XbCrud::make();
        $builder->useCRUD()->alwaysShowPagination(true);
        $builder->setActionConfig('width', '200px');
        $builder->addHeaderDialog('添加角色', Url::make('AdminRole/add'), [
            'title' => '添加角色',
        ])->level('primary');

        $builder->addColumn('id', '序号')->width(80);
        $builder->addColumn('title', '角色名称')->minWidth(180);
        $builder->addColumn('num', '管理员人数', [
            'minWidth' => 100,
        ]);
        $builder->addColumn('sort', '排序', [
            'minWidth' => 100,
        ]);
        $builder->addColumn('create_at', '创建时间', [
            'width' => 150,
        ]);

        $builder->setActionConfig('width', 150);
        $builder->addRightActionDialog('权限', Url::make('auth'), [
            'size' => 'lg',
            'title' => '给「${title}」分配权限',
        ])->className('text-success');
        $builder->addRightActionDialog('修改', Url::make('edit'), [
            'title' => '修改角色',
        ]);
        $builder->addRightActionConfirm('删除', Url::make('del'));
        return $this->successRes($builder);
    }

    /**
     * 添加
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function add(Request $request)
    {
        if ($request->method() == 'POST') {
            $adminId = $request->uid;
            $post = $request->post();

            // 数据验证
            xbValidate(AdminRoleValidate::class, $post, 'add');

            // 设置旗下角色
            $post['admin_id'] = $adminId;
            // 获取默认菜单
            $where = [
                'is_default' => '20',
            ];
            $rules = AdminRule::where($where)->order('sort asc')->column('path');
            $post['rule'] = json_encode($rules);

            $model = new AdminRole;
            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            return $this->success('保存成功');
        }
        $builder = $this->formView();
        $builder->setSaveMethod('POST');
        return $this->successRes($builder);
    }

    /**
     * 修改
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function edit(Request $request)
    {
        $id = $request->get('id');
        $model = AdminRole::where('id', $id)->find();
        if (!$model) {
            return $this->fail('该数据不存在');
        }
        if ($request->method() == 'PUT') {
            $post = $request->post();

            // 数据验证
            xbValidate(AdminRoleValidate::class, $post, 'edit');

            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            return $this->success('保存成功');
        }
        $builder = $this->formView();
        $builder->setSaveMethod('PUT');
        $builder->setData($model);
        return $this->successRes($builder);
    }

    /**
     * 删除
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function del(Request $request)
    {
        $id = $request->get('id');
        $model = AdminRole::where('id', $id)->find();
        if (!$model) {
            return $this->fail('该数据不存在');
        }
        // 检测是否系统管理员
        if ($model['is_system'] == '20') {
            return $this->fail('无法删除系统角色');
        }
        // 检测是否有管理员
        if (Admin::where('role_id', $id)->count()) {
            return $this->fail('该角色下存在管理员，无法删除');
        }
        // 删除管理员
        if (!$model->delete()) {
            return $this->fail('删除失败');
        }
        return $this->success('删除成功');
    }

    /**
     * 分配权限
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function auth(Request $request)
    {
        $id = $request->get('id');
        $model = AdminRole::where('id', $id)->find();
        if (!$model) {
            return $this->fail('该数据不存在');
        }
        if ($request->method() === 'PUT') {
            $rules = $request->post('rules');
            if (empty($rules)) {
                return $this->fail('请选择权限规则');
            }
            $rules = is_array($rules) ? $rules : explode(',', $rules);
            $rules = json_encode($rules, 256);
            if (!$model->save(['rule' => $rules])) {
                return $this->fail('权限分配失败');
            }
            return $this->success('权限分配成功');
        }
        // 解析已选择菜单规则
        $activeRules = $model['rule'] ? json_decode($model['rule'], true) : [];
        $activeRules = is_array($activeRules) ? implode(',', $activeRules) : $activeRules;
        $builder = XbForm::make();
        // 获取权限权限
        $rules = AdminRule::order('sort asc')->select()->toArray();
        $rules = MenuChecked::menu2DToTree($rules);
        $rules = $this->getRules($rules);
        /** @var Transfer */
        $transfer = Transfer::make();
        $builder->addRowGroup('role', [
            $builder->addRowInput('title', '角色名称', '', [
                'static' => true,
            ])->columnRatio(6),
            $builder->addRowInput('sort', '角色排序', '100')->columnRatio(6),
        ]);
        $builder->addRowGroup('auth', [
            // 设置穿索器
            $transfer
                ->name('rules')
                ->resultListModeFollowSelect((true))
                ->selectMode('tree')
                ->selectTitle('权限列表')
                ->resultTitle('已选权限')
                ->columns([
                    ['name' => 'label', 'label' => '权限名称'],
                    ['name' => 'value', 'label' => '权限地址'],
                ])
                ->options($rules)
                ->value($activeRules)
        ]);
        $builder->setSaveMethod('PUT');
        return $this->successRes($builder);
    }

    /**
     * 获取权限规则
     * @param array $data
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function getRules(array $data)
    {
        $list = [];
        $i = 0;
        foreach ($data as $value) {
            if (empty($value['children'])) {
                $list[$i]['label'] = $value['title'];
                $list[$i]['value'] = $value['path'];
            } else {
                $list[$i]['label'] = $value['title'];
                // 递归获取子节点
                $list[$i]['children'] = $this->getRules($value['children']);
            }
            $i++;
        }
        return $list;
    }

    /**
     * 表单视图
     * @return XbForm
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function formView()
    {
        $builder = XbForm::make();
        $builder->addRowInput('title', '角色名称');
        $builder->addRowInput('sort', '角色排序', '100');
        return $builder;
    }
}
