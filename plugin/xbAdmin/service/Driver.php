<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\service;

use think\Exception;
use Webman\Http\UploadFile;

/**
 * 存储模块驱动
 *
 * 依据储存配置解析出具体的储存引擎类并转发文件操作，是业务层与储存引擎之间的门面。
 */
class Driver
{
    /**
     * 上传配置
     * @var array
     */
    private $config;

    /**
     * 当前存储引擎类
     * @var Server|null
     */
    private $engine = null;

    /**
     * 构造方法
     * @param array $config
     * @param string|null $storage
     */
    protected function __construct(array $config, ?string $storage = null)
    {
        $this->config = $config;
        $this->engine = $this->getEngineClass($storage);
    }

    /**
     * 创建实例
     * @param array $config 上传配置
     * @param string|null $storage 指定存储方式
     * @return Driver
     */
    public static function make(array $config, ?string $storage = null)
    {
        return new static($config, $storage);
    }

    /**
     * 设置上传的文件信息
     * @param string|UploadFile $name 文件名称或上传文件对象
     * @param array $extension 允许的文件扩展名
     * @return mixed
     */
    public function setUploadFile(string|UploadFile $name, array $extension = [])
    {
        return $this->engine->setUploadFile($name, $extension);
    }

    /**
     * 设置上传的文件信息（通过文件路径）
     * @param mixed $filePath
     * @return mixed
     */
    public function setUploadFileByReal($filePath)
    {
        return $this->engine->setUploadFileByReal($filePath);
    }

    /**
     * 执行文件上传
     * @param string $save_dir 保存路径
     * @param array|null $callback 回调参数
     * @return mixed
     */
    public function upload(string $save_dir, ?array $callback = null)
    {
        return $this->engine->upload($save_dir, $callback);
    }

    /**
     * 检测文件是否存在
     * @param string $filePath 文件路径
     * @return bool
     */
    public function exist(string $filePath)
    {
        return $this->engine->exist($filePath);
    }

    /**
     * 获取文件URL
     * @param string $filePath 文件路径
     * @return string
     */
    public function url(string $filePath)
    {
        return $this->engine->url($filePath);
    }

    /**
     * 获取文件签名URL
     * @param string $filePath
     * @return string
     */
    public function getSignUrl(string $filePath)
    {
        return $this->engine->getSignUrl($filePath);
    }

    /**
     * 获取服务域名
     * @return string
     */
    public function domain()
    {
        return $this->engine->domain();
    }

    /**
     * 抓取网络资源
     * @param mixed $url
     * @param mixed $key
     * @return mixed
     */
    public function fetch($url, $key)
    {
        return $this->engine->fetch($url, $key);
    }

    /**
     * 执行文件删除
     * @param string $fileName
     * @return mixed
     */
    public function delete(string $fileName)
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
     * 设置上传后的文件名
     * @param string $fileName
     * @return mixed
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
     * @param string|null $storage 指定存储方式，如不指定则为系统默认
     * @return Server
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
        // 除 plugin 标识外没有任何参数，说明该引擎尚未在后台完成配置
        if (!array_diff_key($config, ['plugin' => 1])) {
            throw new Exception("储存引擎（{$engineName}）尚未配置，请先在「储存设置」中填写参数后重试");
        }
        // 引擎名称首字母转大写
        $engineName = ucfirst($engineName);
        // 获取存储引擎类
        $class = "\\plugin\\{$config['plugin']}\\engine\\{$engineName}Server";
        // 检测存储引擎类是否存在
        if (!class_exists($class)) {
            throw new Exception('未找到存储引擎类: ' . $engineName);
        }
        return new $class($config);
    }
}