<?php
namespace app\admin\controller;

use app\builder\FormBuilder;
use app\providers\UploadProvider;
use app\XbController;
use hg\apidoc\annotation as Apidoc;
use app\model\Upload;
use support\Request;

/**
 * 附件管理
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class UploadController extends XbController
{
    /**
     * 附件列表
     * @Apidoc\Method ("GET")
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        $suffix = $request->get('suffix', '*');
        $order  = $request->get('order', 'desc');
        $page   = (int)$request->get('page', 1);
        $limit  = (int)$request->get('limit', 10);

        // 查询条件组装
        $where = [
            ['uid','=', 0],
        ];
        // 取出对后缀格式
        if ($suffix !== '*' && !empty($suffix))
        {
            $where[] = ['format', 'in', $suffix];
        }
        $data = Upload::where($where)
            ->order("update_at {$order},id asc")
            ->paginate([
                'page' => $page,
                'list_rows' => $limit,
            ])
            ->toArray();
        return parent::successRes($data);
    }

    /**
     * 修改附件
     * @Apidoc\Method ("GET")
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function edit(Request $request)
    {
        $id    = $request->get('id', '');
        $model = Upload::where('id', $id)->find();
        if (!$model)
        {
            return $this->fail('该附件不存在');
        }
        if ($request->method() === 'POST')
        {
            $post = $request->post();
            if (!$model->save($post))
            {
                return $this->fail('修改失败');
            }
            return $this->success('修改成功');
        }
        else
        {
            $view = $this->formView()
                ->setMethod('POST')
                ->setData($model)
                ->create();
            return $this->successRes($view);
        }
    }

    /**
     * 删除选中附件
     * @Apidoc\Method ("POST,PUT,DELETE")
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function del(Request $request)
    {
        $id  = (int)$request->post('id', 0);
        $ids = $request->post('ids', []);
        if (empty($ids) && empty($id)) {
            return $this->fail('请选择删除的附件');
        }
        if (!empty($id) && empty($ids)) {
            $ids = [$id];
        }
        $paths = Upload::where('id', 'in', $ids)->column('path');
        if (empty($paths)) {
            return $this->fail('附件不存在');
        }
        // 删除附件
        UploadProvider::delete($paths);
        // 返回数据
        return $this->success('删除完成');
    }

    /**
     * 上传附件
     * @Apidoc\Method ("POST")
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function upload(Request $request)
    {
        // 获取上传文件
        $file = $request->file('file');
        // 上传附件
        $data = UploadProvider::upload($file);
        if (!$data)
        {
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
