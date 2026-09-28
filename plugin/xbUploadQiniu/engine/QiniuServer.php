<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @version  1.0
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbUploadQiniu\engine;

use Exception;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use Qiniu\Storage\BucketManager;
use plugin\xbAdmin\service\Server;

/**
 * 七牛云存储引擎
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class QiniuServer extends Server
{
    /**
     * 执行上传
     * @param string $save_dir
     * @param array|null $callback
     * @throws Exception
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function upload(string $save_dir, ?array $callback)
    {
        if (!class_exists('Qiniu\Auth')) {
            throw new Exception('请安装七牛云SDK');
        }
        if (!class_exists('Qiniu\Storage\UploadManager')) {
            throw new Exception('请安装七牛云SDK');
        }
        // 要上传文件的本地路径
        $realPath = $this->getRealPath();

        // 构建鉴权对象
        $auth = new Auth($this->config['access_key'], $this->config['secret_key']);

        // 要上传的空间
        $token = $auth->uploadToken($this->config['bucket']);

        // 初始化 UploadManager 对象并进行文件的上传
        $uploadMgr = new UploadManager();

        try {
            // 调用 UploadManager 的 putFile 方法进行文件的上传
            $key = $save_dir . '/' . $this->fileName;
            list(, $error) = $uploadMgr->putFile($token, $key, $realPath);

            if ($error !== null) {
                $this->error = $error->message();
                return false;
            }
            return true;
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
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
        if (!class_exists('Qiniu\Auth')) {
            throw new Exception('请安装七牛云SDK');
        }
        if (!class_exists('Qiniu\Storage\UploadManager')) {
            throw new Exception('请安装七牛云SDK');
        }
        $auth = new Auth($this->config['access_key'], $this->config['secret_key']);
        $bucketManager = new BucketManager($auth);
        $files = $bucketManager->stat($this->config['bucket'], $filePath);
        $files = array_filter($files);
        return empty($files) ? false : true;
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
     * @param string $filePath 文件路径
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getSignUrl(string $filePath)
    {
        if (!class_exists('Qiniu\Auth')) {
            throw new Exception('请安装七牛云SDK');
        }
        $auth = new Auth($this->config['access_key'], $this->config['secret_key']);
        return $auth->privateDownloadUrl($this->format($this->config['domain'], $filePath), 3600);
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
        try {
            if (!class_exists('Qiniu\Auth')) {
                throw new Exception('请安装七牛云SDK');
            }
            if (!class_exists('Qiniu\Storage\UploadManager')) {
                throw new Exception('请安装七牛云SDK');
            }
            if (substr($url, 0, 1) !== '/' || strstr($url, 'http://') || strstr($url, 'https://')) {
                $auth = new Auth($this->config['access_key'], $this->config['secret_key']);
                $bucketManager = new BucketManager($auth);
                list(, $err) = $bucketManager->fetch($url, $this->config['bucket'], $key);
            } else {
                $auth = new Auth($this->config['access_key'], $this->config['secret_key']);
                $token = $auth->uploadToken($this->config['bucket']);
                $uploadMgr = new UploadManager();
                list(, $err) = $uploadMgr->putFile($token, $key, $url);
            }

            if ($err !== null) {
                $this->error = $err->message();
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
            return false;
        }
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
        if (!class_exists('Qiniu\Auth')) {
            throw new Exception('请安装七牛云SDK');
        }
        if (!class_exists('Qiniu\Storage\UploadManager')) {
            throw new Exception('请安装七牛云SDK');
        }
        // 构建鉴权对象
        $auth = new Auth($this->config['access_key'], $this->config['secret_key']);
        // 初始化 BucketManager 对象并进行文件的删除
        $bucketMgr = new BucketManager($auth);

        try {
            list($res, $error) = $bucketMgr->delete($this->config['bucket'], $fileName);
            if ($error !== null) {
                $this->error = $error->message();
                return false;
            }
            return true;
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
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
     * 设置文件名称
     * @param string $fileName 文件名称
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setFileName(string $fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }
}