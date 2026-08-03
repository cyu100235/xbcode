<?php
/**
 * 积木云渲染器
 * @package  XbCode.0
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbUpload\bootstrap;

use Webman\Bootstrap;
use Workerman\Protocols\Http;
use Workerman\Worker;

/**
 * 设置上传临时目录
 * 避免 tempnam() 在系统临时目录创建文件而产生警告
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class UploadTmpDir implements Bootstrap
{
    /**
     * @param Worker|null $worker
     * @return void
     */
    public static function start(?Worker $worker)
    {
        $uploadTmpDir = runtime_path('/temp');
        if (!is_dir($uploadTmpDir)) {
            mkdir($uploadTmpDir, 0777, true);
        }
        Http::uploadTmpDir($uploadTmpDir);
    }
}
