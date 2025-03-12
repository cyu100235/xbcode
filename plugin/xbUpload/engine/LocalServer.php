<?php
namespace plugin\xbUpload\engine;

use plugin\xbUpload\service\Server;

/**
 * 本地文件驱动
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class LocalServer extends Server
{    
    /**
     * 上传
     * @param string $path
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function upload($path)
    {
        // 上传目录
        $saveDir = public_path() . '/' . $path;
        // 检测目录是否存在
        if (!is_dir($saveDir)) {
            mkdir($saveDir, 0777, true);
        }
        // 储存文件地址
        $filePath = "{$saveDir}/{$this->fileName}";
        // 验证文件并上传
        $info = $this->file->move($filePath);
        if (empty($info)) {
            // $this->error = $this->file->getError();
            return false;
        }
        return true;
    }

    /**
     * 抓取网络资源
     * @param mixed $url
     * @param mixed $key
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function fetch($url, $key=null) {}

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
            $fileName = substr_replace($fileName,"",0,1);
        }
        $filePath = public_path() . "{$fileName}";
        return !file_exists($filePath) ?: unlink($filePath);
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
