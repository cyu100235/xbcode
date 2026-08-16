<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbUpload\app\admin\controller;

use support\Request;
use plugin\xbCode\api\Url;
use plugin\xbUpload\api\Files;
use plugin\xbCode\XbController;
use plugin\xbUpload\api\UploadApi;
use plugin\xbUpload\api\UploadChunk;
use plugin\xbUpload\app\model\Upload;
use plugin\xbUpload\enum\UploadExtEnum;
use plugin\xbCode\builder\Renders\XbForm;
use plugin\xbCode\builder\Renders\XbCrud;

/**
 * 附件管理
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class UploadController extends XbController
{
    /**
     * 列表
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function index(Request $request)
    {
        $act = $request->get('_act', '');
        if ($act) {
            $type = $request->get('_nav', '');
            $adapter = $request->get('name', '');

            // 查询条件组装
            $where = [
                // 查询系统附件
                ['uid', '=', 0],
                ['adapter', '=', $adapter],
            ];
            // 取出对后缀格式
            if ($type) {
                $suffix = UploadExtEnum::getFieldValue($type, '', 'ext');
                $suffix = explode(',', $suffix);
                if ($suffix) {
                    $where[] = ['format', 'in', $suffix];
                }
            }
            $data = Upload::where($where)->order("update_at desc")->paginate();
            return $this->successData($data);
        }
        $builder = XbCrud::make(function (XbCrud $builder) {
            // 设置上传附件按钮
            $builder->addHeaderDialog('上传附件', Url::make('upload'))
                ->cancelActions()->title('上传附件');
            // 添加表格列
            $builder->addColumn('id', '序号')->width(80);
            $builder->addColumn('title', '附件名称');
            $builder->addColumn('name', '文件名称');
            $builder->addColumnImage('url', '封面预览')->thumbMode('cover')
                ->showToolbar(true)
                ->enlargeAble(true);
            $builder->addColumn('md5', '唯一指纹')->width(280)->copyable(true);
            $builder->addColumn('format', '文件格式')->width(100)->align('center');
            $builder->addColumn('size_format', '文件大小')->width(120)->align('center');
            $builder->addColumn('create_at', '上传时间')->width(150);
            // 设置扩展操作按钮
            $builder->addBulkActionConfirm('批量删除', Url::make('del'), [
                'method' => 'DELETE'
            ])->confirmText('是否确认批量删除该附件？')->danger();
            // 设置操作按钮
            $builder->setActionConfig('width', 150);
            $builder->addRightActionDialog('查看', Url::make('show'), [
                'title' => '查看附件',
                'actions' => [],
            ])->dark(true);
            $builder->addRightActionDialog('修改', Url::make('edit'), [
                'title' => '修改附件',
            ])->primary(true);
            $builder->addRightActionConfirm('删除', Url::make('DELETE:del'))->danger(true);
        });
        // 设置侧边栏
        $category = UploadExtEnum::options();
        $category = array_merge([['value' => '', 'label' => '全部']], $category);
        $builder->addSidebars($category);
        return $this->successRes($builder);
    }

    /**
     * 修改
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function edit(Request $request)
    {
        $id = $request->get('id', '');
        $model = Upload::where('id', $id)->find();
        if (!$model) {
            return $this->fail('该附件不存在');
        }
        if ($request->method() === 'PUT') {
            $post = $request->post();
            if (!$model->save($post)) {
                return $this->fail('修改失败');
            }
            return $this->success('修改成功');
        }
        $builder = $this->formView();
        $builder->setSaveMethod('PUT');
        $builder->setData($model);
        return $this->successRes($builder);
    }

    /**
     * 删除
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function del(Request $request)
    {
        $ids = $request->input('ids', []);
        // 检测是否批量删除
        if (empty($ids)) {
            $id = $request->input('id', 0);
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
        Files::make()->make()->delete($data);
        // 返回数据
        return $this->success('删除完成');
    }

    /**
     * 查看
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function show(Request $request)
    {
        $id = $request->get('id', '');
        $model = Upload::where('id', $id)->find();
        if (!$model) {
            return $this->fail('该附件不存在');
        }
        $builder = XbForm::make();
        $builder->addRowInput('title', '附件名称');
        $builder->addRowInput('name', '文件名称')->disabled(true);
        $builder->addRowInput('format', '文件格式')->disabled(true);
        $builder->addRowInput('size_format', '文件大小')->disabled(true);
        $builder->addRowInput('adapter', '储存位置')->disabled(true);

        $extEnum = UploadExtEnum::dict();
        $imageExt = $extEnum['image'] ?? '';
        if (str_contains($imageExt, $model->format)) {
            $builder->addRowImage('url', '图片预览', $model->url)
                ->type('static-image')
                ->thumbMode('cover')
                ->showToolbar(true)
                ->enlargeAble(true);
        }
        $builder->useForm()->static(true);
        $builder->setData($model);
        return $this->successRes($builder);
    }

    /**
     * 上传附件
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function upload(Request $request)
    {
        if (request()->method() == 'POST') {
            $uid = (int) $request->post('uid', 0);
            $name = (string) $request->post('name', 'file');
            $adapter = (string) $request->post('adapter');
            // 上传附件
            $result = UploadApi::make($adapter)
                ->setUid($uid)
                ->upload($name);
            if (!$result) {
                return $this->fail('上传失败');
            }
            return $this->successRes($result);
        }
        return $this->display();
    }

    /**
     * 上传分片
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function chunk(Request $request)
    {
        $act = $request->get('_act');
        if (empty($act)) {
            return $this->fail('缺少操作参数');
        }
        $class = UploadChunk::make();
        if (!method_exists($class, $act)) {
            return $this->fail('操作方法不存在');
        }
        // 调用分片上传方法
        $data = call_user_func([$class, $act], $request);
        return $this->successRes($data);
    }

    /**
     * 表单视图
     * @return XbForm
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function formView()
    {
        $builder = XbForm::make();
        $builder->addRowInput('title', '附件名称');
        $builder->addRowInput('uri', '文件地址')->disabled(true);
        $builder->addRowInput('name', '文件名称')->disabled(true);
        $builder->addRowInput('format', '文件格式')->disabled(true);
        $builder->addRowInput('size_format', '文件大小')->disabled(true);
        $builder->addRowInput('adapter', '储存位置')->disabled(true);
        return $builder;
    }
}
