<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\api;

use RuntimeException;
use Webman\Http\UploadFile;

/**
 * 附件上传 HTTP 客户端
 *
 * 附件由后台统一存储，插件不直接依赖后台的附件类，而是通过网关上传（统一走 Gateway 出口）：
 *   - 上传接口：POST {GATEWAY_URL}/xbAdmin/api/Upload/upload（multipart/form-data）
 *
 * 网关指向本机且端口与本应用一致时（后台与插件同进程部署，见 Gateway::selfHosted()），
 * 直调后台附件接口，避免请求内同步自调用造成的死锁（Windows 单进程下必然发生）。
 *
 * 对外 API：
 *   Upload::upload('file');                                  // 上传当前请求中的文件
 *   Upload::file('/path/to/a.jpg');                          // 上传服务器本地文件
 *   Upload::file('/path/to/a.jpg', ['uid' => 5]);            // 指定归属用户
 *   Upload::file('/path/to/a.jpg', ['adapter' => 'local']);  // 指定储存引擎
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class Upload
{
    /** @var string 后台上传接口路径 */
    protected const UPLOAD_PATH = '/xbAdmin/api/Upload/upload';

    /**
     * 上传当前请求中的文件
     * @param string $name 表单字段名
     * @param array $params 附加参数，支持 uid（归属用户）与 adapter（储存引擎）
     * @throws RuntimeException
     * @return array 附件信息，含 uri / url / md5 / size / format / adapter 等
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function upload(string $name = 'file', array $params = []): array
    {
        if (Gateway::selfHosted()) {
            return self::localApi($params)->upload($name);
        }
        $file = request()->file($name);
        if (!$file instanceof UploadFile) {
            throw new RuntimeException('上传文件不存在');
        }
        return self::send(
            $file->getPathname(),
            (string) ($file->getUploadName() ?: $file->getFilename()),
            (string) $file->getUploadMimeType(),
            $params
        );
    }

    /**
     * 上传服务器本地文件
     * @param string $path 文件绝对路径
     * @param array $params 附加参数，支持 uid（归属用户）与 adapter（储存引擎）
     * @throws RuntimeException
     * @return array 附件信息，含 uri / url / md5 / size / format / adapter 等
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function file(string $path, array $params = []): array
    {
        if (!is_file($path)) {
            throw new RuntimeException('上传文件不存在：' . $path);
        }
        if (Gateway::selfHosted()) {
            return self::localApi($params)->uploadFilePath($path);
        }
        return self::send($path, basename($path), (string) (mime_content_type($path) ?: ''), $params);
    }

    /**
     * 获取后台附件接口（网关与本应用同源时使用）
     * @param array $params 附加参数，支持 uid（归属用户）与 adapter（储存引擎）
     * @throws RuntimeException
     * @return object
     */
    protected static function localApi(array $params): object
    {
        $class = 'plugin\\xbAdmin\\api\\UploadApi';
        if (!class_exists($class)) {
            throw new RuntimeException('上传文件失败，未安装 xbAdmin 后台插件');
        }
        $api = $class::make((string) ($params['adapter'] ?? ''));
        return $api->setUid((int) ($params['uid'] ?? 0));
    }

    /**
     * 以 multipart/form-data 上传文件到后台
     * @param string $path 本地文件路径
     * @param string $filename 客户端文件名
     * @param string $mime 文件类型
     * @param array $params 附加参数，支持 uid（归属用户）与 adapter（储存引擎）
     * @throws RuntimeException
     * @return array 响应中的 data
     */
    protected static function send(string $path, string $filename, string $mime, array $params): array
    {
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new RuntimeException('读取上传文件失败：' . $path);
        }
        $multipart = [];
        $uid = (int) ($params['uid'] ?? 0);
        if ($uid > 0) {
            $multipart[] = ['name' => 'uid', 'contents' => (string) $uid];
        }
        $adapter = trim((string) ($params['adapter'] ?? ''));
        if ($adapter !== '') {
            $multipart[] = ['name' => 'adapter', 'contents' => $adapter];
        }
        $multipart[] = [
            'name' => 'file',
            'contents' => $handle,
            'filename' => $filename,
            'headers' => ['Content-Type' => $mime !== '' ? $mime : 'application/octet-stream'],
        ];
        try {
            // 上传大文件耗时较长，请求级超时放宽到 300 秒
            return Gateway::request('POST', self::UPLOAD_PATH, [
                'multipart' => $multipart,
                'timeout' => 300,
            ], true, '上传文件', '上传文件失败');
        } finally {
            if (is_resource($handle)) {
                fclose($handle);
            }
        }
    }
}