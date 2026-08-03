<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbUpload\app\api\controller;

use Exception;
use support\Request;
use plugin\xbCode\XbController;
use plugin\xbUpload\api\UploadApi;
use hg\apidoc\annotation as Apidoc;
use plugin\xbUpload\api\UploadChunk;

/**
 * 附件管理
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class UploadController extends XbController
{
    #[
        Apidoc\Title("上传附件"),
        Apidoc\Method("POST"),
        Apidoc\ParamType("formdata"),
        Apidoc\Param("cid", type: "string", desc: "分类ID", require: false),
        Apidoc\Param("name", type: "string", desc: "文件键名", require: false, default: "file"),
        Apidoc\Param("file", type: "file", desc: "上传文件", require: true),
    ]
    public function upload(Request $request)
    {
        if (!$request->isPost()) {
            throw new Exception('请求方法错误');
        }
        $uid = (int) $request->uid;
        $name = (string) $request->post('name', 'file');
        // 上传附件
        $result = UploadApi::make()
            ->setUid($uid)
            ->upload($name);
        if (!$result) {
            return $this->fail('上传失败');
        }
        return $this->successRes($result);
    }

    #[
        Apidoc\Title("上传分片"),
        Apidoc\Method("POST"),
        Apidoc\Param("string", "_act", "上传操作", "start开始上传，chunk上传分片，finish完成上传"),
        Apidoc\Param("file", "file", "文件分片（仅上传分片时需要）", ""),
    ]
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
}
