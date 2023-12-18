<?php

namespace app\common\manager;

use app\common\builder\FormBuilder;
use app\common\model\Upload;
use app\common\service\UploadService;
use think\Request;

trait UploadMgr
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
        $cid    = $request->get('cid', '');
        $suffix = $request->get('suffix', '*');
        $order  = $request->get('order', 'desc');

        # 查询条件组装
        $where = [];
        # 取出对后缀格式
        if ($suffix !== '*' && !empty($suffix)) {
            $where[] = ['format', 'in', $suffix];
        }
        if ($cid) {
            $where[] = ['cid', '=', $cid];
        }
        $data = Upload::with(['category'])
            ->where($where)
            ->order("update_at {$order},id asc")
            ->paginate()
            ->toArray();
        return parent::successRes($data);
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
        $id    = $request->get('id', '');
        $model = Upload::where('id', $id)->find();
        if (!$model) {
            return $this->fail('该附件不存在');
        }
        if ($request->isPost()) {
            $post = $request->post();
            if (!$model->save($post)) {
                return $this->fail('修改失败');
            }
            return $this->success('修改成功');
        } else {
            $view = $this->formView()
                ->setMethod('POST')
                ->setData($model)
                ->create();
            return $this->successRes($view);
        }
    }

    /**
     * 删除选中附件
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function del(Request $request)
    {
        $id  = (int) $request->post('id', 0);
        $ids = $request->post('ids', []);
        if (!$id && empty($ids)) {
            return $this->fail('请选择需要删除的附件');
        }
        if (empty($ids)) {
            if (!UploadService::delete($id)) {
                return $this->fail('删除失败');
            }
        }
        # 批量删除
        foreach ($ids as $id) {
            UploadService::delete($id);
        }
        return $this->success('删除完成');
    }


    /**
     * 上传附件
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function upload(Request $request)
    {
        # 获取上传文件
        $file = $request->file('file');
        # 获取上传目录
        $dirName = $request->post('dir_name', '');
        # 上传附件
        $data = UploadService::upload($file, $dirName);
        if (!$data) {
            return $this->fail('上传失败');
        }
        return $this->successFul('上传成功', $data);
    }

    /**
     * 移动选中附件
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function move(Request $request)
    {
        $cid = $request->post('cid');
        $ads = $request->post('ids');
        if (!$cid) {
            return $this->fail('请选择移动的分类');
        }
        if (!$ads) {
            return $this->fail('附件选择错误');
        }
        if (!is_array($ads)) {
            return $this->fail('请选择移动的附件');
        }
        $where[] = ['id', 'in', $ads];
        Upload::where($where)->save(['cid' => $cid]);
        return $this->success('附件移动完成');
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
