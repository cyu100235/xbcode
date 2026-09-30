<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbAdmin\app\api\controller;

use support\Request;
use support\Response;
use plugin\xbAdmin\api\Files;
use plugin\xbAdmin\api\UploadApi;
use plugin\xbAdmin\api\UploadChunk;
use plugin\xbAdmin\app\model\Upload;
use plugin\xbAdmin\app\BaseController;
use plugin\xbAdmin\enum\UploadExtEnum;
use plugin\xbAdmin\exception\business\ExceptionBusiness;

/**
 * 附件接口
 * @copyright 贵州云铺网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class UploadController extends BaseController
{
    /**
     * 附件列表
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $adapter = (string) $request->get('name', '');
        $type = (string) $request->get('_nav', '');
        // 查询条件组装
        $where = [
            // 查询系统附件
            ['uid', '=', 0],
        ];
        if ($adapter !== '') {
            $where[] = ['adapter', '=', $adapter];
        }
        // 取出对应后缀格式
        if ($type) {
            $suffix = UploadExtEnum::extensions((string) $type);
            if ($suffix) {
                $where[] = ['format', 'in', $suffix];
            }
        }
        $data = Upload::where($where)->order('update_at desc')->paginate();
        return $this->successData($data);
    }

    /**
     * 查询附件
     * @param Request $request
     * @return Response
     */
    public function find(Request $request)
    {
        $model = $this->findModel((int) $request->get('id', 0));
        return $this->successRes($model->toArray());
    }

    /**
     * 修改附件
     * @param Request $request
     * @throws ExceptionBusiness
     * @return Response
     */
    public function edit(Request $request)
    {
        $model = $this->findModel((int) $request->get('id', 0));
        $post = (array) $request->post();
        if (!$model->save($post)) {
            throw new ExceptionBusiness('修改失败');
        }
        return $this->success('修改成功');
    }

    /**
     * 删除附件
     * @param Request $request
     * @throws ExceptionBusiness
     * @return Response
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
            throw new ExceptionBusiness('请选择删除的附件');
        }
        $data = Upload::whereIn('id', $ids)->column('uri');
        if (empty($data)) {
            throw new ExceptionBusiness('附件不存在');
        }
        // 删除附件
        Files::make()->delete($data);
        // 返回数据
        return $this->success('删除完成');
    }

    /**
     * 上传附件
     * @param Request $request
     * @return Response
     */
    public function upload(Request $request)
    {
        $uid = (int) $request->post('uid', 0);
        $name = (string) $request->post('name', 'file');
        $adapter = (string) $request->post('adapter', '');
        // 上传附件
        $result = UploadApi::make($adapter)
            ->setUid($uid)
            ->upload($name);
        if (!$result) {
            return $this->fail('上传失败');
        }
        return $this->successRes($result);
    }

    /**
     * 上传分片
     * @param Request $request
     * @return Response
     */
    public function chunk(Request $request)
    {
        $act = (string) $request->get('_act', '');
        if (empty($act)) {
            return $this->fail('缺少操作参数');
        }
        // 指定上传落地的储存引擎
        $adapter = (string) $request->post('adapter', '');
        $class = UploadChunk::make($adapter);
        if (!method_exists($class, $act)) {
            return $this->fail('操作方法不存在');
        }
        // 调用分片上传方法
        $data = call_user_func([$class, $act], $request);
        return $this->successRes($data);
    }

    /**
     * 按ID查找附件
     * @param int $id
     * @throws ExceptionBusiness
     * @return Upload
     */
    protected function findModel(int $id): Upload
    {
        if ($id <= 0) {
            throw new ExceptionBusiness('该附件不存在');
        }
        $model = Upload::where('id', $id)->find();
        if (!$model) {
            throw new ExceptionBusiness('该附件不存在');
        }
        return $model;
    }
}