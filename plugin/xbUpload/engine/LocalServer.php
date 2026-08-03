<?php
/**
 * 积木云渲染器
 * @package  XbCode.0
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbUpload\engine;

use Exception;
use plugin\xbCode\api\ConfigApi;
use plugin\xbCode\api\Url;
use plugin\xbUpload\service\Server;

/**
 * 本地文件驱动
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class LocalServer extends Server
{
    /**
     * 上传文件到本地目录
     * @param string $save_dir 上传目录路径
     * @param array $callback 上传回调函数
     * @throws Exception
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function upload(string $save_dir, ?array $callback = null)
    {
        // 上传目录
        $path = public_path() . '/' . $save_dir;
        // 检测目录是否存在
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        // 储存文件地址
        $filePath = "{$path}/{$this->fileName}";
        // 验证文件并上传
        $info = $this->file->move($filePath);
        if (empty($info)) {
            throw new Exception('文件上传失败');
        }
        return [];
    }

    /**
     * 检测文件是否存在
     * @param string $filePath 文件路径
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function exist(string $filePath)
    {
        return file_exists(public_path($filePath));
    }

    /**
     * 获取文件URL
     * @param string $filePath
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function url(string $filePath)
    {
        $domain = $this->domain();
        return $this->format($domain, $filePath);
    }

    /**
     * 获取签名URL
     * @param string $filePath 文件地址
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
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function domain()
    {
        $request = request();
        if ($request) {
            $schema = Url::make('')->getSchema();
            $host = $request->host();
            $domain = "{$schema}://" . $host;
        } else {
            $domain = ConfigApi::make('system')->get('system.web_url', '');
        }
        return $domain;
    }

    /**
     * 抓取网络资源
     * @param mixed $url
     * @param mixed $key
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function fetch(string $url, ?string $key = null)
    {
    }

    /**
     * 删除文件
     * @param $fileName
     * @return bool|mixed
     */
    public function delete($fileName)
    {
        $check = strpos($fileName, '/');
        if ($check !== false && $check == 0) {
            // 文件所在目录
            $fileName = substr_replace($fileName, "", 0, 1);
        }
        $filePath = public_path() . "{$fileName}";
        if (!file_exists($filePath)) {
            return unlink($filePath);
        }
        return true;
    }

    /**
     * 设置文件名
     * @param string $fileName
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setFileName(string $fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * 返回文件路径
     * @return mixed
     */
    public function getFileName()
    {
        return $this->fileName;
    }
}
