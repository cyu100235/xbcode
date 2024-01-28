<?php

namespace app\common\manager;

use app\common\builder\FormBuilder;
use app\common\builder\ListBuilder;
use app\common\model\Admin;
use app\common\utils\MenusUtil;
use Exception;
use think\Request;
use app\common\model\AdminRole;

trait AdminRoleMgr
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
        $data = $builder
            ->addActionOptions('操作')
            ->pageConfig()
            ->addTopButton('add', '添加', [
                'type'          => 'modal',
                'api'           => '/AdminRole/add',
                'path'          => '/AdminRole/add',
            ], [
                'title'         => '添加角色',
            ], [
                'type'          => 'primary'
            ])
            ->addRightButton('auth', '授权', [
                'type'          => 'modal',
                'api'           => '/AdminRole/auth',
                'path'          => '/AdminRole/auth',
            ], [
                'title'         => '权限设置',
            ], [
                'type'          => 'warning',
            ])
            ->addRightButton('edit', '修改', [
                'type'          => 'modal',
                'api'           => '/AdminRole/edit',
                'path'          => '/AdminRole/edit',
            ], [], [
                'type'          => 'primary',
            ])
            ->addRightButton('del', '删除', [
                'type'          => 'confirm',
                'api'           => '/AdminRole/del',
                'method'        => 'delete',
            ], [
                'type'          => 'error',
                'title'         => '温馨提示',
                'content'       => '是否确认删除该数据',
            ], [
                'type'          => 'danger',
            ])
            ->addColumn('create_at', '创建时间', [
                'width'         => 160,
            ])
            ->addColumn('title', '角色名称')
            ->addColumn('rule', '拥有权限')
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
        $adminId  = $request->userId;
        $where      = [
            'pid'           => $adminId,
        ];
        $data = AdminRole::where($where)->paginate()->toArray();
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
        if ($request->method() == 'POST') {
            $post = $request->post();
            $admin_id = $request->user['id'];
            if (empty($post['title'])) {
                return $this->fail('角色名称不能为空');
            }
            $post['pid'] = $admin_id;
            // 默认权限
            $post['rule'] = MenusUtil::getDefaultRule();
            $model        = new AdminRole;
            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            return $this->success('保存成功');
        }
        $view = $this->formView()->setMethod('POST')->create();
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
        $id = $request->get('id');
        $where = [
            ['id', '=', $id],
        ];
        $model = $this->model->where($where)->find();
        if (!$model) {
            return $this->fail('数据不存在');
        }
        if ($request->method() == 'PUT') {
            $post = $request->post();
            if (empty($post['title'])) {
                return $this->fail('角色名称不能为空');
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
        $model = AdminRole::where(['id' => $id])->find();
        if (!$model) {
            return $this->fail('数据不存在');
        }
        if ($model->is_system == '20') {
            throw new Exception('系统角色无法删除');
        }
        if (Admin::where('role_id',$id)->find()) {
            throw new Exception('该角色下存在管理员，无法删除');
        }
        if (!$model->delete()) {
            return $this->fail('删除失败');
        }
        return $this->success('删除成功');
    }

    /**
     * 角色授权
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function auth(Request $request)
    {
        $id = $request->get('id');
        $where = [
            ['id', '=', $id],
        ];
        $model = $this->model->where($where)->find();
        if (!$model) {
            return $this->fail('角色不存在');
        }
        if ($request->method() == 'PUT') {
            $post = $request->post();
            if (empty($post['rule'])) {
                return $this->fail('规则授权错误');
            }
            $rules = $post['rule'];
            $model->rule = array_values(array_filter($rules));
            if (!$model->save()) {
                return parent::fail('保存失败');
            }
            return parent::success('保存成功');
        }
        // 获取全部权限规则
        $rules = MenusUtil::getOriginMenus();
        // 拼接规则权限为视图所需要的数组
        $authRule = $this->getAuthRule($rules);
        // 获取默认规则
        $defaultActive = MenusUtil::getDefaultRule();
        $defaultActive = empty($defaultActive) ? [] : $defaultActive;
        // 默认选中规则 与 已授权规则合并
        if (!empty($defaultActive)) {
            $model['rule'] = array_values(array_unique(array_merge($defaultActive, $model['rule'])));
        }
        // 渲染页面
        $builder = new FormBuilder;
        $view = $builder
            ->setMethod('PUT')
            ->addRow('title', 'input', '角色名称', '', [
                'disabled'                  => true
            ])
            ->addRow('rule', 'tree', '权限授权', [], [
                // 节点数据
                'data'                      => $authRule,
                // 是否默认展开所有节点
                'defaultExpandAll'          => true,
                // 	在显示复选框的情况下，是否严格的遵循父子不互相关联的做法，默认为 false
                'checkStrictly'             => true,
                // 每个树节点用来作为唯一标识的属性，整棵树应该是唯一的
                'nodeKey'                   => 'value',
            ])
            ->setData($model)
            ->create();
        return parent::successRes($view);
    }

    /**
     * 获取表单视图
     * @return FormBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function formView()
    {
        $builder = new FormBuilder;
        $builder->addRow('title', 'input', '角色名称');
        return $builder;
    }

    /**
     * 获取权限列表
     * @param array $rule
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    private function getAuthRule(array $rule): array
    {
        $data = [];
        $i = 0;
        foreach ($rule as $value) {
            // 默认选选中
            $disabled = $value['is_default'] === '20' ? true : false;
            // 组装树状格式数据
            $label                = $value['title'];
            if ($value['path']) {
                $label .= "（{$value['path']}）";
            }
            $data[$i]['title']          = $label;
            $data[$i]['value']          = $value['path'];
            $data[$i]['disabled']       = $disabled;
            if ($value['children']) {
                $data[$i]['children']   = $this->getAuthRule($value['children']);
            }
            $i++;
        }
        return $data;
    }
}
