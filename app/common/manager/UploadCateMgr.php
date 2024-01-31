<?php

namespace app\common\manager;

use app\common\builder\FormBuilder;
use app\common\model\Upload;
use app\common\model\UploadCate;
use app\common\validate\UploadCateValidate;
use think\Request;

trait UploadCateMgr
{
    /**
     * 列表
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        $order = $request->get('order', 'asc');
        $appName = $request->appName ?? null;
        $where = [];
        if (!$appName) {
            $where[] = ['saas_appid', '=', null];
        }
        $data = UploadCate::where($where)
        ->order("sort {$order},id asc")
        ->select()
        ->toArray();
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
        if ($request->isPost()) {
            $post = $request->post();
            $post['is_system']  = '10';

            // 数据验证
            xbValidate(UploadCateValidate::class, $post, 'add');

            $model = new UploadCate;
            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            return $this->success('保存成功');
        }
        $view = $this->formView()
        ->setMethod('POST')
        ->create();
        return $this->successRes($view);
    }

    /**
     * 编辑
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function edit(Request $request)
    {
        $id    = $request->get('id', '');
        $model = UploadCate::where('id',$id)->find();
        if (!$model) {
            return $this->fail('该分类不存在');
        }
        if ($request->isPut()) {
            $post = $request->post();

            // 数据验证
            xbValidate(UploadCateValidate::class, $post, 'edit');

            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            return $this->success('保存成功');
        }
        $view = $this->formView()
        ->setMethod('PUT')
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
        $id    = $request->post('id', '');
        if (!$id) {
            return $this->fail('缺少参数');
        }
        $model = UploadCate::where('id',$id)->find();
        if (!$model) {
            return $this->fail('该附件分类不存在');
        }
        if ($model->is_system === '20') {
            return $this->fail('系统分类，禁止删除');
        }
        if (Upload::where('cid',$id)->count()) {
            return $this->fail('该分类下已有附件，禁止删除');
        }
        if (!$model->delete()) {
            return $this->fail('删除失败');
        }
        return $this->success('删除成功');
    }

    /**
     * 视图
     * @return FormBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function formView()
    {
        $builder = new FormBuilder;
        $builder->addRow('title', 'input', '分类名称');
        $builder->addRow('dir_name', 'input', '分类标识', '', [
            'disabled' => true,
        ]);
        $builder->addRow('sort', 'input', '分类排序');
        return $builder;
    }
}
