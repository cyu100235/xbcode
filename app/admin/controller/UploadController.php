<?php
namespace app\admin\controller;

use app\model\Upload;
use app\model\UploadCate;
use xbcode\builder\FormBuilder;
use xbcode\providers\DictProvider;
use xbcode\providers\FileProvider;
use xbcode\providers\UploadProvider;
use xbcode\XbController;
use support\Request;

/**
 * 附件管理
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class UploadController extends XbController
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
        $cid   = $request->get('cid', '');
        $type  = $request->get('type', '*');
        $order = $request->get('order', 'desc');

        // 查询条件组装
        $where = [
            ['uid', '=', 0],
        ];
        // 取出对应分类
        if ($cid !== '') {
            $where[] = ['cid', '=', $cid];
        }
        // 取出对后缀格式
        if ($type !== '*') {
            $suffix = DictProvider::get('uploadFileFormat')->dict();
            if (isset($suffix[$type])) {
                $suffix  = $suffix[$type];
                $suffix  = is_array($suffix) ? $suffix : explode(',', $suffix);
                $where[] = ['format', 'in', $suffix];
            }
        }
        $data = Upload::where($where)->order("update_at {$order},id asc")->paginate();
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
        $id    = $request->get('id', '');
        $model = Upload::where('id', $id)->find();
        if (!$model) {
            return $this->fail('该附件不存在');
        }
        if ($request->method() === 'POST') {
            $post = $request->post();
            if (!$model->save($post)) {
                return $this->fail('修改失败');
            }
            return $this->success('修改成功');
        } else {
            $builder = $this->formView();
            $builder->setMethod('POST');
            $builder->setData($model);
            $data = $builder->create();
            return $this->successRes($data);
        }
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
        $ids = $request->post('ids', []);
        // 检测是否批量删除
        if (empty($ids)) {
            $id = $request->post('id', 0);
            if ($id) {
                $ids = [$id];
            }
        }
        if (empty($ids)) {
            return $this->fail('请选择删除的附件');
        }
        $data = Upload::whereIn('id', $ids)->column('uri');
        if (empty($data)) {
            return $this->fail('附件不存在');
        }
        // 删除附件
        FileProvider::delete($data);
        // 返回数据
        return $this->success('删除完成');
    }

    /**
     * 移动附件
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function move(Request $request)
    {
        if ($request->method() == 'PUT') {
            $cid = $request->post('cid', '');
            $ids = $request->get('ids', '');
            if (empty($cid)) {
                return $this->fail('请选择目标分类');
            }
            if (empty($ids)) {
                return $this->fail('请选择需要移动的数据');
            }
            // 批量移动
            Upload::whereIn('id', $ids)->save(['cid' => $cid]);
            // 返回数据
            return $this->success('移动成功');
        }
        $category = UploadCate::order('sort asc,id asc')->column('id as value, title as label');
        $builder  = new FormBuilder;
        $builder->addRow('cid', 'select', '目标分类', '', [
            'options' => $category
        ]);
        $builder->setMethod('PUT');
        $builder->setPosition('left');
        $data = $builder->create();
        return $this->successRes($data);
    }

    /**
     * 上传附件
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function upload(Request $request)
    {
        $cid = (int) $request->post('cid', 0);
        $uid = (int) $request->post('uid', 0);
        // 上传附件
        $data = UploadProvider::upload('file', $cid, $uid);
        if (!$data) {
            return $this->fail('上传失败');
        }
        return $this->successFul('上传成功', $data);
    }

    /**
     * 表单视图
     * @return FormBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function formView()
    {
        $builder = new FormBuilder;
        $builder->addRow('title', 'input', '附件名称');
        $builder->addRow('path', 'input', '文件地址', '', [
            'disabled' => true,
        ]);
        $builder->addRow('filename', 'input', '文件名称', '', [
            'disabled' => true,
            'col' => 12
        ]);
        $builder->addRow('format', 'input', '文件格式', '', [
            'disabled' => true,
            'col' => 12
        ]);
        $builder->addRow('size_format', 'input', '文件大小', '', [
            'disabled' => true,
            'col' => 12
        ]);
        $builder->addRow('adapter', 'input', '选定器', '', [
            'disabled' => true,
            'col' => 12
        ]);
        return $builder;
    }
}
