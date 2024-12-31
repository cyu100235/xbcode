<?php
namespace app\backend\controller;

use app\model\Upload;
use app\model\UploadCate;
use app\validate\UploadCateValidate;
use xbcode\builder\FormBuilder;
use xbcode\XbController;
use support\Request;

/**
 * 附件库分类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class UploadCateController extends XbController
{
    /**
     * 列表
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        $order = $request->get('order', 'asc');
        $where = [];
        $data  = UploadCate::where($where)
        ->order("sort {$order},id asc")
        ->select()
        ->toArray();
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
        if ($request->method() == 'POST'){
            $post              = $request->post();
            $post['is_system'] = '10';

            // 数据验证
            xbValidate(UploadCateValidate::class, $post, 'add');

            $model = new UploadCate;
            if (!$model->save($post)){
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
     * 编辑
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function edit(Request $request)
    {
        $id    = $request->get('id', '');
        $model = UploadCate::where('id', $id)->find();
        if (!$model){
            return $this->fail('该分类不存在');
        }
        if ($request->method() == 'PUT'){
            $post = $request->post();

            // 数据验证
            xbValidate(UploadCateValidate::class, $post, 'edit');

            if (!$model->save($post)){
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
        $id = $request->post('id', '');
        if (!$id){
            return $this->fail('缺少参数');
        }
        $model = UploadCate::where('id', $id)->find();
        if (!$model){
            return $this->fail('该附件分类不存在');
        }
        if ($model->is_system === '20'){
            return $this->fail('系统分类，禁止删除');
        }
        if (Upload::where('cid', $id)->count()){
            return $this->fail('该分类下已有附件，禁止删除');
        }
        if (!$model->delete()){
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
        $isEdit = request()->get('id') ? true : false;
        $builder->addRow('dir_name', 'input', '目录名称', '', [
            'disabled' => $isEdit,
        ]);
        $builder->addRow('sort', 'input', '分类排序','100');
        return $builder;
    }
}
