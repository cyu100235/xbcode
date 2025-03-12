<?php
namespace plugin\xbUpload\service;

use think\Exception;

/**
 * 存储模块驱动
 * Class driver
 * @package app\common\library\storage
 */
class Driver
{
    /**
     * 上传配置
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private $config;

    /**
     * 当前存储引擎类
     * @var 
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private $engine;

    /**
     * 构造方法
     * @param array $config
     * @param string $storage
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function __construct(array $config, string $storage = null)
    {
        $this->config = $config;
        $this->engine = $this->getEngineClass($storage);
    }
    
    /**
     * 设置上传的文件信息
     * @param string $name
     * @param array $extension
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function setUploadFile(string $name = 'file', array $extension = [])
    {
        return $this->engine->setUploadFile($name,$extension);
    }

    /**
     * 设置上传的文件信息
     * @param string $filePath
     * @return mixed
     */
    public function setUploadFileByReal($filePath)
    {
        return $this->engine->setUploadFileByReal($filePath);
    }

    /**
     * 执行文件上传
     * @param $save_dir (保存路径)
     * @return mixed
     */
    public function upload($save_dir)
    {
        return $this->engine->upload($save_dir);
    }
    
    /**
     * 抓取网络资源
     * @param mixed $url
     * @param mixed $key
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function fetch($url, $key) {
        return $this->engine->fetch($url, $key);
    }

    /**
     * 执行文件删除
     * @param $fileName
     * @return mixed
     */
    public function delete($fileName)
    {
        return $this->engine->delete($fileName);
    }

    /**
     * 获取错误信息
     * @return mixed
     */
    public function getError()
    {
        return $this->engine->getError();
    }

    /**
     * 获取文件路径
     * @return mixed
     */
    public function getFileName()
    {
        return $this->engine->getFileName();
    }

    /**
     * 返回文件信息
     * @return mixed
     */
    public function getFileInfo()
    {
        return $this->engine->getFileInfo();
    }

    /**
     * 获取当前的存储引擎
     * @param null|string $storage 指定存储方式，如不指定则为系统默认
     * @return mixed
     * @throws Exception
     */
    private function getEngineClass(string $storage = null)
    {
        // 获取存储引擎名称
        $engineName = is_null($storage) ? $this->config['default'] : $storage;
        // 获取使用中的存储引擎配置
        $config = $this->config['engine'][$engineName] ?? [];
        if (empty($config)) {
            throw new Exception('未找到存储引擎配置: ' . $engineName);
        }
        // 引擎名称首字母转大写
        $engineName = ucfirst($engineName);
        $class = "\\plugin\\{$config['plugin']}\\engine\\{$engineName}Server";
        // 检测存储引擎类是否存在
        if (!class_exists($class)) {
            throw new Exception('未找到存储引擎类: ' . $engineName);
        }
        return new $class($config);
    }
}
