<?php
namespace plugin\xbCode\app\admin\controller;

use support\Request;
use plugin\xbCode\XbController;
use plugin\xbCode\app\model\Admin;
use plugin\xbCode\api\MenuChecked;
use plugin\xbCode\app\model\AdminRule;
use plugin\xbCode\app\model\AdminRole;
use plugin\xbCode\builder\FormBuilder;
use plugin\xbCode\builder\ListBuilder;
use plugin\xbCode\app\validate\AdminRoleValidate;

/**
 * 角色管理
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class AdminRoleController extends XbController
{
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
            'width' => 230,
        ]);
        $builder->pageConfig();
        $builder->addTopButton('add', '添加', [
            'type' => 'modal',
            'api' => xbUrl('AdminRole/add'),
            'path' => xbUrl('AdminRole/add'),
        ], [
            'title' => '添加角色',
            'customStyle' => [
                'width' => '400px',
            ],
        ], [
            'type' => 'primary',
        ]);
        $builder->addRightButton('auth', '权限', [
            'type' => 'modal',
            'api' => xbUrl('AdminRole/auth'),
            'path' => xbUrl('AdminRole/auth'),
        ], [
            'title' => '分配角色权限',
            'customStyle' => [
                'width' => '500px',
                'height' => '70vh',
            ],
        ], [
            'type' => 'success',
            'icon' => 'EditPen',
        ]);
        $builder->addRightButton('edit', '修改', [
            'type' => 'modal',
            'api' => xbUrl('AdminRole/edit'),
            'path' => xbUrl('AdminRole/edit'),
        ], [
            'title' => '修改角色',
            'customStyle' => [
                'width' => '400px',
            ],
        ], [
            'type' => 'primary',
            'icon' => 'Edit',
        ]);
        $builder->addRightButton('del', '删除', [
            'type' => 'confirm',
            'api' => xbUrl('AdminRole/del'),
            'path' => xbUrl('AdminRole/del'),
            'method' => 'delete',
        ], [
            'type' => 'error',
            'title' => '温馨提示',
            'content' => '是否确认删除该数据？',
        ], [
            'type' => 'danger',
            'icon' => 'Delete',
        ]);
        $builder->addColumn('id', '序号', [
            'width' => 100,
        ]);
        $builder->addColumn('title', '角色名称', [
            'minWidth' => 180,
        ]);
        $builder->addColumn('num', '管理员人数', [
            'minWidth' => 100,
        ]);
        $builder->addColumn('sort', '排序', [
            'minWidth' => 100,
        ]);
        $builder->addColumn('create_at', '创建时间', [
            'width' => 160,
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

            // 数据验证
            xbValidate(AdminRoleValidate::class, $post, 'add');

            // 设置旗下角色
            $post['admin_id'] = $adminId;
            // 获取默认菜单
            $where = [
                'is_default' => '20',
            ];
            $rules = AdminRule::where($where)->order('sort asc')->column('path');
            $post['rule'] = $rules;

            $model = new AdminRole;
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
        $id = $request->post('id');
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
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function auth(Request $request)
    {
        $id = $request->get('id');
        $model = AdminRole::where('id', $id)->find();
        if (!$model) {
            return $this->fail('该数据不存在');
        }
        if ($request->method() === 'PUT') {
            $rules = $request->post('rule', []);
            if (empty($rules)) {
                return $this->fail('请选择权限规则');
            }
            if (!$model->save(['rule' => $rules])) {
                return $this->fail('权限分配失败');
            }
            return $this->success('权限分配成功');
        }
        $builder = new FormBuilder;
        $builder->setMethod('PUT');
        $builder->addRow('title', 'input', '角色名称', '', [
            'disabled' => true,
        ]);
        // 检测是否有权限
        $model['rule'] = empty($model['rule']) ? [] : $model['rule'];
        // 获取全部权限规则
        $rules = AdminRule::order('sort asc')->select()->toArray();
        // 转换成树状结构
        $rules = MenuChecked::menu2DToTree($rules);
        // 获取默认规则
        $defaultRules = AdminRule::where('is_default', '20')->column('path');
        // 解析权限规则
        $authRule = $this->parseAuthRule($defaultRules, $rules);
        // 添加权限规则
        $builder->addRow('rule', 'tree', '权限授权', [], [
            // 节点数据
            'data' => $authRule,
            // 是否默认展开所有节点
            'defaultExpandAll' => true,
            // 	在显示复选框的情况下，是否严格的遵循父子不互相关联的做法，默认为 false
            // 'checkStrictly' => true,
            // 每个树节点用来作为唯一标识的属性，整棵树应该是唯一的
            'nodeKey' => 'value',
        ]);
        $builder->setData($model);
        $data = $builder->create();
        return $this->successRes($data);
    }

    /**
     * 获取权限规则
     * @param array $defaultRules
     * @param array $rules
     * @return array[]
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function parseAuthRule(array $defaultRules, array $rules)
    {
        $data = [];
        $i = 0;
        foreach ($rules as $value) {
            // 组装树状格式数据
            $label = $value['title'];
            if ($value['path']) {
                $label .= "（{$value['path']}）";
            }
            // 节点名称
            $data[$i]['title'] = $label;
            // 节点地址
            $data[$i]['value'] = $value['path'];

            // 默认选选中
            $disabled = in_array($value['path'], $defaultRules) ? true : false;
            $data[$i]['disabled'] = $disabled;
            // 是否有子节点
            if ($value['children']) {
                $data[$i]['children'] = $this->parseAuthRule($defaultRules, $value['children']);
            }
            $i++;
        }
        return $data;
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
        $builder->addRow('title', 'input', '角色名称');
        $builder->addRow('sort', 'input', '角色排序', '100');
        return $builder;
    }
}
