<?php
namespace app\admin\controller;

use app\admin\validate\ProjectValidate;
use app\common\BaseController;
use app\common\builder\FormBuilder;
use think\facade\Db;
use think\Request;

/**
 * 项目管理
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class ProjectsController extends BaseController
{
    /**
     * 项目列表
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function index()
    {
    }

    /**
     * 添加
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function add(Request $request)
    {
        if ($request->isPost()) {
            # 获取表单数据
            $post = $request->post();
            # 数据验证
            xbValidate(ProjectValidate::class, $post, 'add');
            # 开启事务
            Db::startTrans();
            try {
                # 获取应用TOKEN
                # 创建管理员
                # 创建项目
                Db::commit();
            } catch (\Throwable $e) {
                Db::rollback();
                throw $e;
            }
            return $this->success('添加成功');
        }
        $builder = $this->formView();
        $builder->setMethod('POST');
        $view = $builder->create();
        return $this->successRes($view);
    }
    public function edit()
    {
    }
    public function del()
    {
    }

    /**
     * 表单视图
     * @param mixed $edit
     * @return FormBuilder
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    private function formView($edit = false)
    {
        $builder = new FormBuilder;
        $builder->setMethod('POST');
        $builder->addRow('title', 'input', '项目名称', '', [
            'col'           => 12,
        ]);
        if ($edit) {
            $builder->addRow('name', 'info', '所属应用', '', [
                'col'           => 12,
            ]);
        } else {
            $builder->addRow('name', 'select', '所属应用', '', [
                'col'           => 12,
                'noDataText'    => '您还没有更多的应用',
            ]);
        }
        $builder->addRow('username', 'input', '超管账号', '', [
            'col' => 12,
        ]);
        $builder->addRow('password', 'input', '登录密码', '', [
            'col' => 12,
        ]);
        $builder->addRow('logo', 'uploadify', '项目图标', '', [
            'col' => 12,
            'props' => [
                'type' => 'image',
                'format' => ['jpg', 'jpeg', 'png']
            ],
        ]);
        return $builder;
    }
}
