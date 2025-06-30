<?php

namespace plugin\xbUpload\api;

use Exception;
use think\facade\Db;
use plugin\xbUpload\api\Files;
use plugin\xbCode\api\ConfigApi;
use plugin\xbUpload\service\Driver;
use plugin\xbUpload\app\model\Upload;
use plugin\xbUpload\enum\UploadExtEnum;

/**
 * 附件接口类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class UploadApi
{
    /**
     * 上传文件
     * @param string $name 文件key
     * @param int $cid 分类id
     * @param int $uid 用户id
     * @param string $adapter 适配器
     * @throws \Exception
     * @return array|Upload|\think\db\Query|\think\Model
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function upload(string $name = 'file',int $cid = 0, int $uid = 0, string $adapter = '')
    {
        try {
            // 1.获取上传适配器
            if (empty($adapter)) {
                $adapter = ConfigApi::get('upload.active', 'local');
            }
            // 2.检测是否站点上传
            $appid = request()->saas_appid ?? null;
            if ($appid) {
                // 本地储存权限检测
                $local = Db::name('web_site')->where('id', $appid)->value('local', '10');
                if ($local === '10' && $adapter === 'local') {
                    throw new Exception('您没有上传本地储存权限');
                }
            }
            // 3.获取上传配置
            $config = EngineApi::getConfig($adapter);
            // 4.获取上传文件信息
            $driver = new Driver($config);
            $driver->setUploadFile($name);
            $fileName = $driver->getFileName();
            $fileInfo = $driver->getFileInfo();
            $ext      = $fileInfo['ext'] ?? '';
            $md5 = md5_file($fileInfo['realPath']);
            // 5.检测文件是否已存在
            $where = [
                'md5' => $md5,
                'cid' => $cid,
                'uid' => $uid,
                'adapter' => $config['default'],
            ];
            $file = Upload::where($where)->find();
            if ($file) {
                // 检测文件是否存在
                $filePath = public_path() . "/{$file->uri}";
                if (file_exists($filePath)) {
                    $file->update_at = date('Y-m-d H:i:s');
                    $file->save();
                    return $file;
                }
            }
            // 6.上传文件
            $saveDir = self::getUploadPath($ext);
            if (!$driver->upload($saveDir)) {
                throw new Exception($driver->getError());
            }
            // 7.处理文件信息
            if (strlen($fileInfo['name']) > 128) {
                $temp             = substr($fileInfo['name'], 0, 123);
                $nameEnd          = substr($fileInfo['name'], strlen($fileInfo['name']) - 5, strlen($fileInfo['name']));
                $fileInfo['name'] = $temp . $nameEnd;
            }
            // 8.保存文件信息
            $file = Upload::create([
                'cid' => $cid,
                'uid' => $uid,
                'title' => $fileInfo['name'],
                'name' => $fileInfo['name'],
                'md5' => $md5,
                'size' => $fileInfo['size'],
                'format' => $ext,
                'adapter' => $config['default'],
                'uri' => "{$saveDir}/{$fileName}",
                'url' => Files::url($saveDir),
                'value' => Files::url($saveDir),
            ]);
            // 9.返回文件信息
            return $file;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * 本地上传
     * @param string $saveDir 保存目录
     * @param string $name 文件上传标识
     * @param string $fileName 文件名称
     * @throws \Exception
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function uploadLocal(string $saveDir, string $name = null, string $fileName = null)
    {
        // 检测目录是否存在
        if (!is_dir($saveDir)) {
            mkdir($saveDir, 0777, true);
        }
        // 获取文件key
        $name = $name ?: 'file';
        // 获取文件
        $file  = request()->file($name);
        if (empty($file)) {
            throw new Exception('上传文件不存在');
        }
        $size = $file->getSize();
        $md5 = md5_file($file->getRealPath());
        $extension = strtolower($file->getUploadExtension());
        if (empty($fileName)) {
            $fileName = $file->getUploadName();
            $hashName = "{$md5}.{$extension}";
            $filePath = "{$saveDir}/{$hashName}";
        }else{
            $filePath = "{$saveDir}/{$fileName}";
        }
        // 验证文件并上传
        $info = $file->move($filePath);
        if (empty($info)) {
            throw new Exception('上传失败');
        }
        $data = [
            'title' => $fileName,
            'name' => $fileName,
            'md5' => $md5,
            'size' => $size,
            'format' => $extension,
            'adapter' => 'local',
            'path' => $filePath,
            'uri' => str_replace(base_path() . '/', '', $filePath),
        ];
        return $data;
    }
    
    /**
     * 下载文件
     * @param string $url 下载地址，示例：uploads/image/20250515
     * @param string $savePath 保存路径
     * @param int $uid 操作用户
     * @param int $cid 分类ID
     * @throws Exception
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function download(string $url, string $savePath = null,int $uid = 0,int $cid = 0)
    {
        if (empty($url)) {
            throw new Exception('下载地址不能为空');
        }
        $fileName = basename($url);
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        if (empty($savePath)) {
            $path = static::getUploadPath($extension);
        }else{
            $path = $savePath;
        }
        // 储存目录
        $dirPath = public_path() . "/{$path}";
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0755, true);
        }
        $adapter = 'local';
        $where = [
            'cid' => $cid,
            'uid' => $uid,
            'adapter' => $adapter,
        ];
        $filePath = "{$dirPath}/{$fileName}";
        if (file_exists($filePath)) {
            $where['md5'] = md5_file($filePath);
            $file = Upload::where($where)->find();
            if ($file && empty($savePath)) {
                return $path;
            }
        }
        // 下载文件
        file_put_contents($filePath, fopen($url, 'r'));
        if (!file_exists($filePath)) {
            throw new Exception('下载文件失败');
        }
        // 获取文件信息
        $fileName = md5_file($filePath);
        $fileName = "{$fileName}.{$extension}";
        $newPath = "{$dirPath}/{$fileName}";
        rename($filePath, $newPath);
        $filePath = $newPath;

        // 是否保存至附件库
        if (empty($savePath)) {
            // 保存文件信息
            Upload::create([
                'cid' => $cid,
                'uid' => $uid,
                'title' => $fileName,
                'name' => $fileName,
                'md5' => md5_file($filePath),
                'size' => filesize($filePath),
                'format' => strtolower($extension),
                'adapter' => 'local',
                'uri' => $path,
            ]);
        }
        return $path;
    }

    /**
     * 获取上传文件储存路径
     * @param string $extension
     * @return string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function getUploadPath(string $extension)
    {
        $name = self::getDictDirName($extension);
        return "uploads/{$name}/" . date('Ymd');
    }

    /**
     * 获取上传文件储存目录名
     * @param string $extension
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function getDictDirName(string $extension)
    {
        $uploadFormat = UploadExtEnum::dict();
        foreach ($uploadFormat as $name => $value) {
            $format = explode(',', $value);
            if (in_array($extension, $format)) {
                return $name;
            }
        }
        return 'other';
    }
}