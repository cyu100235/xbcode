<?php
namespace plugin\xbUpload\app\admin\controller;

use support\Request;
use plugin\xbCode\builder\Builder;
use plugin\xbUpload\api\Files;
use plugin\xbCode\XbController;
use plugin\xbCode\builder\Renders\Form;
use plugin\xbCode\builder\Renders\Grid;
use plugin\xbUpload\api\UploadApi;
use plugin\xbUpload\app\model\Upload;
use plugin\xbUpload\enum\CategoryEnum;
use plugin\xbUpload\enum\UploadExtEnum;

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
                $suffix = UploadExtEnum::dict();
                $suffix = $suffix[$type] ?? '';
                if($suffix){
                    $where[] = ['format', 'in', $suffix];
                }
            }
            $data = Upload::where($where)->order("update_at desc")->paginate();
            return $this->successData($data);
        }
        $builder = Builder::crud(function (Grid $builder) {
            // 开启选择
            $builder->useCRUD()->selectable(true);
            // 开启多选
            $builder->useCRUD()->multiple(true);
            // 设置表格ID
            $builder->useCRUD()->id('crud-table');

            // 设置上传附件按钮
            $builder->addHeaderUpload('上传附件',xbUrl('Upload/upload'), [
                'id' => 'crud',
                'onEvent' => [
                    'success' => [
                        'actions' => [
                            'type' => 'clear',
                            'componentId' => 'crud-table',
                        ],
                    ]
                ],
            ]);
            // 设置扩展操作按钮
            $builder->addBulkActionConfirm('批量删除',xbUrl('Upload/del'))->confirmText('是否确认批量删除该附件？')->level('danger');

            // 设置操作按钮
            $builder->setCRUDActionConfig('width', 200);
            $builder->addActionDialogBtn('查看', xbUrl('Upload/show'),[
                'dialog' => [
                    'title' => '查看附件',
                    'size' => 'default',
                    'bodyClassName' => 'height-auto',
                    'actions' => [],
                ],
            ])->level('primary');
            $builder->addActionDialogBtn('修改', xbUrl('Upload/edit'),[
                'dialog' => [
                    'title' => '修改附件',
                    'size' => 'default',
                    'bodyClassName' => 'height-auto',
                ]
            ])->level('primary');
            $builder->addActionConfirmBtn('删除', xbUrl('Upload/del'))->level('danger');

            // 添加表格列
            $builder->addColumn('id', '序号')->width('100px');
            $builder->addColumn('title', '附件名称');
            $builder->addColumn('name', '文件名称');
            $builder->addColumnImage('url', '图片预览')->thumbMode('cover')->showToolbar(true)->enlargeAble(true);
            $builder->addColumn('md5', '唯一指纹')->width('280px')->copyable(true);
            $builder->addColumn('format', '文件格式')->width('80px');
            $builder->addColumn('size_format', '文件大小')->width('130px');
            $builder->addColumn('create_at', '上传时间')->width('180px');
        });
        // 设置侧边栏
        $category = CategoryEnum::options();
        $category = array_merge([['value' => '', 'label' => '全部']], $category);
        $builder->useNavs(xbUrl('Upload/index', $request->get()))->links($category);
        return $this->successRes($builder);
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
        $builder->setMethod('PUT');
        $builder->setData($model);
        return $this->successRes($builder);
    }

    /**
     * 查看
     * @param \support\Request $request
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
        $builder = Builder::form(function(Form $builder)use($model){
            $builder->addRowInput('title', '附件名称');
            $builder->addRowInput('name', '文件名称')->disabled(true);
            $builder->addRowInput('format', '文件格式')->disabled(true);
            $builder->addRowInput('size_format', '文件大小')->disabled(true);
            $builder->addRowInput('adapter', '储存位置')->disabled(true);

            $extEnum = UploadExtEnum::dict();
            $imageExt = $extEnum['image'] ?? '';
            if(str_contains($imageExt, $model->format)){
                $builder->addRowImage('url', '图片预览', $model->url)
                ->type('static-image')
                ->thumbMode('cover')
                ->showToolbar(true)
                ->enlargeAble(true);
            }
        });
        $builder->useForm()->static(true);
        $builder->setData($model);
        return $this->successRes($builder);
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
        Files::delete($data);
        // 返回数据
        return $this->success('删除完成');
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
        $name = $request->post('name', 'file');
        // 上传附件
        $data = UploadApi::upload($name, $cid, $uid);
        if (!$data) {
            return $this->fail('上传失败');
        }
        return $this->successRes($data);
    }
    
    /**
     * 表单视图
     * @return Form
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function formView()
    {
        $builder = Builder::form(function(Form $builder){
            $builder->addRowInput('title', '附件名称');
            $builder->addRowInput('uri', '文件地址')->disabled(true);
            $builder->addRowInput('name', '文件名称')->disabled(true);
            $builder->addRowInput('format', '文件格式')->disabled(true);
            $builder->addRowInput('size_format', '文件大小')->disabled(true);
            $builder->addRowInput('adapter', '储存位置')->disabled(true);
        });
        return $builder;
    }
}
