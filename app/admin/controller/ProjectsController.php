<?php
namespace app\admin\controller;

use app\common\validate\ProjectValidate;
use app\common\BaseController;
use app\common\builder\FormBuilder;
use app\common\model\Admin;
use app\common\model\Projects;
use app\common\service\CloudService;
use Exception;
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
     * @param \think\Request $request
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function index(Request $request)
    {
        $data = Projects::order(['id' => 'desc'])->paginate();
        return $this->successRes($data);
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
            // 获取表单数据
            $post = $request->post();
            // 创建项目
            CloudService::createdProject($post);
            // 返回数据
            return $this->success('项目创建成功');
        }
        $builder = $this->formView();
        $view    = $builder->create();
        return $this->successRes($view);
    }

    /**
     * 修改项目
     * @param \think\Request $request
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function edit(Request $request)
    {
        $id    = $request->get('id');
        $model = Projects::where('id', $id)->find();
        if (!$model) {
            return $this->fail('项目不存在');
        }
        if ($request->isPut()) {
            // 获取表单数据
            $post = $request->post();
            // 数据验证
            xbValidate(ProjectValidate::class, $post, 'edit');
            $model = Projects::where('id', $id)->find();
            if (!$model) {
                return $this->fail('项目不存在');
            }
            $where      = [
                'saas_appid'    => $id,
                'is_system'     => '20',
            ];
            $adminModel = Admin::where($where)->find();
            if (!$adminModel) {
                return $this->fail('项目管理员不存在');
            }
            // 开启事务
            Db::startTrans();
            try {
                // 更新项目
                $data  = [
                    'title' => $post['title'],
                    'name' => $post['name'],
                    'logo' => $post['logo'],
                ];
                if (!$model->save($data)) {
                    throw new Exception('更新项目失败');
                }
                // 更新管理员
                $data       = [
                    'username' => $post['username'],
                ];
                if ($post['password']) {
                    $data['password'] = $post['password'];
                }
                if (!$adminModel->save($data)) {
                    throw new Exception('更新项目管理员失败');
                }
                Db::commit();
            } catch (\Throwable $e) {
                Db::rollback();
                throw $e;
            }
            return $this->success('项目更新成功');
        }
        $where      = [
            'saas_appid'    => $id,
            'is_system'     => '20',
        ];
        $username = Admin::where($where)->value('username','');
        $model['username'] = $username;
        $builder = $this->formView(true);
        $builder->setMethod('PUT');
        $builder->setData($model);
        $view    = $builder->create();
        return $this->successRes($view);
    }

    /**
     * 删除项目
     * @param \think\Request $request
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function del(Request $request)
    {
        $id    = $request->post('id');
        CloudService::deleteProject($id);
        return $this->success('项目删除成功');
    }

    /**
     * 项目登录
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function login(Request $request)
    {
        try {
            $id    = $request->get('id');
            $model = Projects::where('id', $id)->find();
            if (!$model) {
                throw new Exception('项目不存在');
            }
            if (empty($model->app_name)) {
                throw new Exception('项目应用错误');
            }
            $project = $model->toArray();
            $class   = "base\\{$model->app_name}\\Package";
            $url     = call_user_func(
                [$class, 'login'],
                $request,
                $project,
            );
        } catch (\Throwable $e) {
            throw $e;
        }
        return $this->successRes([
            'url' => $url
        ]);
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
        $builder->addRow('name', 'input', '访问标识', '', [
            'col'           => 12,
            'disabled'      => $edit,
        ]);
        $builder->addRow('username', 'input', '超管账号', '', [
            'col' => 12,
        ]);
        $builder->addRow('password', 'input', '登录密码', '', [
            'col' => 12,
        ]);
        if ($edit) {
            $builder->addRow('app_name', 'info', '所属应用', '', [
                'col' => 12,
            ]);
        } else {
            $builder->addRow('app_name', 'select', '所属应用', '', [
                'col' => 12,
                'noDataText' => '您还没有更多的应用',
            ]);
        }
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
