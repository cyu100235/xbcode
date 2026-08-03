<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbUpload\trait;

use plugin\xbUpload\api\UploadApi;
use plugin\xbUpload\api\UploadChunk;
use plugin\xbCode\exception\business\ExceptionUnauthorized;

/**
 * 上传实现器
 * @author 楚羽幽 958416459@qq.com
 * @copyright 贵州积木云网络科技有限公司
 */
trait UploadTrait
{
    /**
     * 上传附件
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function upload()
    {
        if (request()->method() != 'POST') {
            return $this->fail('请求类型错误');
        }
        $uid = (int) request()->uid;
        $name = request()->post('name', 'file');
        if (empty($uid)) {
            throw new ExceptionUnauthorized('用户未登录或登录已超时');
        }
        // 上传附件
        $data = UploadApi::make()->setUid($uid)->upload($name);
        if (!$data) {
            return $this->fail('上传失败');
        }
        return $this->successRes($data);
    }

    /**
     * 上传分片
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function chunk()
    {
        $act = request()->get('_act');
        if (empty($act)) {
            return $this->fail('缺少操作参数');
        }
        $class = UploadChunk::make();
        if (!method_exists($class, $act)) {
            return $this->fail('操作方法不存在');
        }
        // 调用分片上传方法
        $data = call_user_func([$class, $act], request());
        return $this->successRes($data);
    }
}