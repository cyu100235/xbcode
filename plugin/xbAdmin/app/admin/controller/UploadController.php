<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbAdmin\app\admin\controller;

use support\Request;
use support\Response;
use plugin\xbAdmin\api\Url;
use plugin\xbCode\api\Gateway;
use plugin\xbAdmin\app\BaseController;
use plugin\xbAdmin\enum\UploadExtEnum;
use plugin\xbCode\builder\Renders\XbForm;
use plugin\xbCode\builder\Renders\XbCrud;

/**
 * 附件管理
 * @copyright 贵州云铺网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class UploadController extends BaseController
{
    /**
     * 列表
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $act = $request->get('_act', '');
        $adapter = (string) $request->get('name', '');
        if (!$act) {
            $result = Gateway::get('xbAdmin/api/Upload/index', request()->get());
            return $this->response($result);
        }
        $builder = XbCrud::make();
        // 设置上传附件按钮（弹窗视图由 admin 应用渲染，实际上传请求由 api 应用处理）
        $uploadUrl = Url::make('Upload/upload');
        if ($adapter !== '') {
            $uploadUrl->query(['adapter' => $adapter]);
        }
        $builder->addHeaderDialog('上传附件', $uploadUrl)
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
            'method' => 'DELETE',
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
        // 设置侧边栏
        $builder->addSidebars(array_merge([['value' => '', 'label' => '全部']], UploadExtEnum::options()));
        return $this->successRes($builder);
    }

    /**
     * 修改
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request)
    {
        $id = (int) $request->get('id', 0);
        $model = $this->findModel($id);
        if (!$model) {
            return $this->fail('该附件不存在');
        }
        if ($request->method() === 'PUT') {
            $result = Gateway::put("xbAdmin/api/Upload/edit?id={$id}", (array) $request->post());
            return $this->response($result);
        }
        $builder = $this->formView();
        $builder->setSaveMethod('PUT');
        $builder->setData($model);
        return $this->successRes($builder);
    }

    /**
     * 删除
     * @param Request $request
     * @return Response
     */
    public function del(Request $request)
    {
        $params = array_merge((array) $request->get(), (array) $request->post());
        $result = Gateway::delete('xbAdmin/api/Upload/del', $params);
        return $this->response($result);
    }

    /**
     * 查看
     * @param Request $request
     * @return Response
     */
    public function show(Request $request)
    {
        $model = $this->findModel((int) $request->get('id', 0));
        if (!$model) {
            return $this->fail('该附件不存在');
        }
        $builder = XbForm::make();
        $builder->addRowInput('title', '附件名称');
        $builder->addRowInput('name', '文件名称')->disabled(true);
        $builder->addRowInput('format', '文件格式')->disabled(true);
        $builder->addRowInput('size_format', '文件大小')->disabled(true);
        $builder->addRowInput('adapter', '储存位置')->disabled(true);
        $builder->addRowInput('url', '文件地址')->disabled(true)->copyable(['enabled' => true]);

        $imageExt = UploadExtEnum::extensions('image');
        if (in_array((string) ($model['format'] ?? ''), $imageExt, true)) {
            $builder->addRowImage('url', '图片预览', $model['url'] ?? '')
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
     * 上传附件视图
     * @param Request $request
     * @return Response
     */
    public function upload(Request $request)
    {
        return $this->display();
    }

    /**
     * 表单视图
     * @return XbForm
     */
    private function formView(): XbForm
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

    /**
     * 查找附件
     * @param int $id
     * @return array
     */
    private function findModel(int $id): array
    {
        if ($id <= 0) {
            return [];
        }
        $result = Gateway::get("xbAdmin/api/Upload/find?id={$id}");
        return $result['data'] ?? [];
    }
}