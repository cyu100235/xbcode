<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @version  1.0
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbUpload\api;

use support\Request;

/**
 * 分片上传处理
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class UploadChunk
{
    /**
     * 创建实例
     * @return UploadChunk
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function make()
    {
        return new UploadChunk;
    }
    
    /**
     * 开始上传
     * @param \support\Request $request
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function start(Request $request)
    {
        $key = rand(100000, 999999);
        $uploadId = rand(100000, 999999);
        return [
            'key' => $key,
            'uploadId' => $uploadId
        ];
    }
    
    /**
     * 上传分片
     * @param \support\Request $request
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function chunk(Request $request)
    {
        $post = $request->post();
        $tag = md5(rand(100000, 999999));
        return [
            'eTag' => $tag,
        ];
    }
    
    /**
     * 完成上传
     * @param \support\Request $request
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function finish(Request $request)
    {
        return [
            'url' => 'https://xxxx.cdn.bcebos.com/images/JSSDK_page-xxxxx.zip',
            'value' => 'https://xxxx.cdn.bcebos.com/images/JSSDK_page-xxxxx.zip',
        ];
    }
}