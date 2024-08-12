<?php

namespace app\common\providers;

use Shopwwi\WebmanFilesystem\Storage as BaseStorage;
use app\common\providers\upload\BaseUpload;
use app\common\providers\upload\Config;
use app\common\providers\upload\RemoteUpload;
use app\common\providers\upload\Storage;
use Webman\Http\UploadFile;
use app\model\Upload;
use Exception;

/**
 * 附件服务类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class UploadProvider
{
    // 使用基础类
    use BaseUpload;
    // 使用远程文件下载并储存
    use RemoteUpload;

    /**
     * 上传文件
     * @param \Webman\Http\UploadFile $file
     * @param int $uid
     * @param string $adapter
     * @param bool $save
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function upload(UploadFile $file, int $uid = 0, string $adapter = '', bool $save = true)
    {
        // 获取当前文件选定器
        if (!$adapter) {
            $adapter = Config::getDbConfig()['default'] ?? 'public';
        }
        // 检测文件是否已上传
        $md5  = hash_file('md5', $file->getRealPath());
        $data = self::getFileInfo(['md5' => $md5], $adapter);
        if ($data) {
            return $data;
        }
        // 根据文件类型获取储存目录
        $dirName = self::getUploadDir($file->getUploadExtension());
        // 检测上传路径
        if (!$dirName) {
            throw new Exception('文件类型不支持上传');
        }
        // 实例SDK
        $filesystem = Storage::instance();
        // 切换驱动
        if ($adapter) {
            $filesystem = $filesystem->adapter($adapter);
        }
        // 上传文件
        $info = $filesystem->path($dirName)->upload($file);
        // 如果是对象则转数组
        if (is_object($info)) {
            $info = json_decode(json_encode($info), true);
        }
        // 保存文件信息
        $data = [
            'uid'      => $uid,
            'title'    => $info['origin_name'],
            'filename' => $info['origin_name'],
            'md5'      => $md5,
            'format'   => $info['extension'],
            'adapter'  => $adapter,
            'size'     => $info['size'],
            'path'     => $info['file_name'],
            'url'      => $info['file_url'],
        ];
        if (!$save) {
            return $data;
        }
        if (!self::addFileInfo($data)) {
            throw new Exception('文件上传失败');
        }
        return $data;
    }



    /**
     * 临时上传文件
     * @param \Webman\Http\UploadFile $file
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function tempUpload(UploadFile $file)
    {
        // 实例SDK
        /** @var BaseStorage */
        $filesystem = Storage::instance();
        // 切换驱动
        $filesystem = $filesystem->adapter('public');
        // 上传文件
        $dirName = 'uploads/temp';
        $data    = $filesystem->path($dirName)->upload($file);
        // 如果是对象则转数组
        if (is_object($data)) {
            $data = json_decode(json_encode($data), true);
        }
        return $data;
    }

    /**
     * 获取外链地址
     * @param mixed $path
     * @param string $adapter
     * @param mixed $default
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function url(mixed $path, string $adapter = '', $default = null)
    {
        if (empty($path)) {
            return $default;
        }
        // 数组类型URL
        if (is_array($path)) {
            $data = [];
            foreach ($path as $key => $value) {
                $data[$key] = self::url($value, $adapter, $default);
            }
            return $data;
        }
        // 获取驱动SDK
        $fileSystem = Storage::instance();
        // 获取文件所属驱动
        if (empty($adapter)) {
            $adapter = Upload::where('path', $path)->value('adapter', '');
        }
        // 切换驱动
        if ($adapter) {
            $adapter    = Config::$platform[$adapter] ?? 'public';
            $fileSystem = $fileSystem->adapter($adapter);
        }
        // 访问链接
        return $fileSystem->url($path);
    }

    /**
     * 获取附件路径
     * @param string $path
     * @param string $adapter
     * @param mixed $default
     * @return string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function path(mixed $url, string $adapter = '', $default = null)
    {
        if (is_array($url) && count($url) > 0) {
            $data = [];
            if (count($url) === 1) {
                return self::path(current($url));
            }
            foreach ($url as $value) {
                if (filter_var($value, FILTER_SANITIZE_URL) === false) {
                    throw new Exception('URL地址不合法');
                }
                $parseUrl = parse_url($value);
                $data[]   = ltrim($parseUrl['path'], '/');
            }
            return $data;
        }
        if (filter_var($url, FILTER_SANITIZE_URL) === false) {
            throw new Exception('URL地址不合法');
        }
        $parseUrl = parse_url($url);
        $data     = ltrim($parseUrl['path'], '/');
        return $data;
    }

    /**
     * 删除文件
     * @param array|string $paths
     * @param string $adapter
     * @throws \Exception
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function delete(array|string $paths, string $adapter = '')
    {
        // 获取驱动SDK
        $filesystem = Storage::instance();
        // 切换驱动
        if ($adapter) {
            $filesystem = $filesystem->adapter($adapter);
        }
        if (is_string($paths)) {
            $paths = [$paths];
        }
        foreach ($paths as $path) {
            // 查询附件库记录
            $model = Upload::where('path', $path)->find();
            if (!$model) {
                throw new Exception('文件路径参数错误');
            }
            // 删除附件库记录
            $model->delete();
        }
    }
}