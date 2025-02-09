<?php

namespace xbcode\providers;

use Exception;
use think\facade\Db;
use app\model\Upload;
use xbcode\providers\upload\Driver;
use xbcode\providers\upload\UploadTrait;

/**
 * 附件服务类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class UploadProvider
{
    // 上传辅助类
    use UploadTrait;

    /**
     * 上传文件
     * @param string $name 文件表单名称
     * @param int $cid 分类ID
     * @param int $uid 用户ID
     * @param string $adapter 上传适配器
     * @return Upload
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function upload(string $name = 'file',int $cid = 0, int $uid = 0, string $adapter = '')
    {
        try {
            // 1.获取上传适配器
            if (empty($adapter)) {
                $adapter = ConfigProvider::get('upload', 'active', 'local');
            }
            // 2.检测是否站点上传
            $appid = request()->saasAppid ?? null;
            if ($appid) {
                // 本地储存权限检测
                $local = Db::name('web_site')->where('id', $appid)->value('local', '10');
                if ($local === '10' && $adapter === 'local') {
                    throw new Exception('您没有上传本地储存权限');
                }
            }
            // 3.获取上传配置
            $config = [
                'default' => $adapter,
                'engine' => ConfigProvider::get('upload', '', '', ['json' => true]),
            ];
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
                if (file_exists($file->uri)) {
                    $file->update_at = date('Y-m-d H:i:s');
                    $file->save();
                    return $file;
                }
            }
            // 6.上传文件
            $saveDir = self::getUploadPath($ext);
            $saveDir = "{$saveDir}/{$fileName}";
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
                'uri' => $saveDir,
                'url' => FileProvider::url($saveDir),
            ]);
            // 9.返回文件信息
            return $file;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}