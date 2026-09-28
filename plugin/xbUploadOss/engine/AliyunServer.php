<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbUploadOss\engine;

use Exception;
use OSS\OssClient;
use plugin\xbAdmin\service\Server;

/**
 * 阿里云存储引擎(OSS)
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class AliyunServer extends Server
{
    /**
     * 执行上传
     * @param string $save_dir 保存路径
     * @param array|null $callback 回调参数
     * @throws Exception
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function upload(string $save_dir, ?array $callback)
    {
        if (!class_exists('OSS\OssClient')) {
            throw new Exception('请安装阿里云OSS扩展');
        }
        try {
            $ossClient = new OssClient(
                $this->config['access_key'],
                $this->config['secret_key'],
                $this->config['domain'],
                true
            );
            $ossClient->uploadFile(
                $this->config['bucket'],
                $save_dir . '/' . $this->fileName,
                $this->getRealPath()
            );
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
            return false;
        }
        return true;
    }

    /**
     * 检查文件是否存在
     * @param string $filePath 文件路径
     * @throws Exception
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function exist(string $filePath)
    {
        if (!class_exists('OSS\OssClient')) {
            throw new Exception('请安装阿里云OSS扩展');
        }
        try {
            $ossClient = new OssClient(
                $this->config['access_key'],
                $this->config['secret_key'],
                $this->config['domain'],
                true
            );
            return $ossClient->doesObjectExist($this->config['bucket'], $filePath);
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * 获取文件URL
     * @param string $filePath 文件路径
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function url(string $filePath)
    {
        return $this->format($this->config['domain'], $filePath);
    }

    /**
     * 获取签名URL
     * @param string $filePath
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getSignUrl(string $filePath)
    {
        return $this->url($filePath);
    }

    /**
     * 获取服务域名
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function domain()
    {
        return $this->config['domain'];
    }

    /**
     * 抓取远程资源
     * @param mixed $url
     * @param mixed $key
     * @throws Exception
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function fetch($url, $key = null)
    {
        if (!class_exists('OSS\OssClient')) {
            throw new Exception('请安装阿里云OSS扩展');
        }
        try {
            $ossClient = new OssClient(
                $this->config['access_key'],
                $this->config['secret_key'],
                $this->config['domain'],
                true
            );

            $content = file_get_contents($url);
            $ossClient->putObject(
                $this->config['bucket'],
                $key,
                $content
            );
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
            return false;
        }
        return true;
    }

    /**
     * 删除文件
     * @param mixed $fileName
     * @throws Exception
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function delete($fileName)
    {
        if (!class_exists('OSS\OssClient')) {
            throw new Exception('请安装阿里云OSS扩展');
        }
        try {
            $ossClient = new OssClient(
                $this->config['access_key'],
                $this->config['secret_key'],
                $this->config['domain'],
                true
            );
            $ossClient->deleteObject($this->config['bucket'], $fileName);
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
            return false;
        }
        return true;
    }

    /**
     * 返回文件路径
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * 设置上传后的文件名
     * @param string $fileName
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function setFileName(string $fileName)
    {
        $this->fileName = $fileName;
    }
}