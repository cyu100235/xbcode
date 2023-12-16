<?php

namespace app\common\manager;

use app\common\builder\ListBuilder;
use think\Request;
use app\common\model\AdminRole;

trait AdminRoleMgr
{
    /**
     * Saas应用ID
     * @var int
     */
    protected $saas_appid = null;

    /**
     * 模型
     * @var AdminRole
     */
    protected $model = null;

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
                'api'           => '/AdminRole/auth',
                'path'          => '/AdminRole/auth',
            ], [], [
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
            ->addColumn('rule_name', '部门权限')
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
        $data = $this->model->where($where)->paginate()->toArray();
        return $this->successRes($data);
    }
    public function add(Request $request)
    {
    }
    public function edit(Request $request)
    {
    }
    public function del(Request $request)
    {
    }
}
