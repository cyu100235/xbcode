<?php
/**
 * 积木云渲染器
 * @package  XbCode.0
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbUpload\service;

use think\Exception;
use Webman\Http\UploadFile;

/**
 * 存储模块驱动
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
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
    private $engine = null;

    /**
     * 构造方法
     * @param array $config
     * @param string $storage
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function __construct(array $config, ?string $storage = null)
    {
        $this->config = $config;
        $this->engine = $this->getEngineClass($storage);
    }

    /**
     * 创建实例
     * @param array $config 上传配置
     * @param mixed $storage 存储方式
     * @return Driver
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function make(array $config, ?string $storage = null)
    {
        $class = new static($config, $storage);
        return $class;
    }
    
    /**
     * 设置上传的文件信息
     * @param string|UploadFile $name 文件名称或上传文件对象
     * @param array $extension 允许的文件扩展名
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setUploadFile(string|UploadFile $name, array $extension = [])
    {
        return $this->engine->setUploadFile($name, $extension);
    }
    
    /**
     * 设置上传的文件信息(通过文件路径)
     * @param mixed $filePath
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setUploadFileByReal($filePath)
    {
        return $this->engine->setUploadFileByReal($filePath);
    }
    
    /**
     * 执行文件上传
     * @param string $save_dir (保存路径)
     * @param mixed $callback (回调参数)
     * @return mixed
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function upload(string $save_dir, ?array $callback = null)
    {
        return $this->engine->upload($save_dir, $callback);
    }

    /**
     * 检测文件是否存在
     * @param string $filePath 文件路径
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function exist(string $filePath)
    {
        return $this->engine->exist($filePath);
    }

    /**
     * 获取文件URL
     * @param string $filePath 文件路径
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function url(string $filePath)
    {
        return $this->engine->url($filePath);
    }

    /**
     * 获取文件签名URL
     * @param string $filePath
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getSignUrl(string $filePath)
    {
        return $this->engine->getSignUrl($filePath);
    }

    /**
     * 获取服务域名
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function domain()
    {
        return $this->engine->domain();
    }

    /**
     * 抓取网络资源
     * @param mixed $url
     * @param mixed $key
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function fetch($url, $key)
    {
        return $this->engine->fetch($url, $key);
    }
    
    /**
     * 执行文件删除
     * @param string $fileName
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function delete(string $fileName)
    {
        return $this->engine->delete($fileName);
    }
    
    /**
     * 获取错误信息
     * @return mixed
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getError()
    {
        return $this->engine->getError();
    }
    
    /**
     * 获取文件路径
     * @return mixed
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getFileName()
    {
        return $this->engine->getFileName();
    }

    /**
     * 设置上传后的文件名
     * @param string $fileName
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setFileName(string $fileName)
    {
        return $this->engine->setFileName($fileName);
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
    private function getEngineClass(?string $storage = null)
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
        // 获取存储引擎类
        $class = "\\plugin\\{$config['plugin']}\\engine\\{$engineName}Server";
        // 检测存储引擎类是否存在
        if (!class_exists($class)) {
            throw new Exception('未找到存储引擎类: ' . $engineName);
        }
        $instance = new $class($config);
        return $instance;
    }
}
