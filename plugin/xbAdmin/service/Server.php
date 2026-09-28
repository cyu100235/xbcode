<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\service;

use think\Exception;
use Webman\Http\UploadFile;

/**
 * 存储引擎抽象类
 *
 * 约定各储存引擎（本地、云对象存储）必须实现的文件操作，并统一处理上传文件校验、
 * 指纹计算、文件信息收集与保存文件名生成，具体落地方式由子类实现。
 */
abstract class Server
{
    /**
     * 上传配置
     * @var array
     */
    protected $config = [];

    /**
     * 文件信息
     * @var UploadFile|UploadFile[]|null
     */
    protected $file;

    /**
     * 错误信息
     * @var mixed
     */
    protected $error;

    /**
     * 保存的文件名
     * @var string
     */
    protected $fileName;

    /**
     * 文件信息
     * @var array
     */
    protected $fileInfo;

    /**
     * 是否为系统内部上传
     * @var bool
     */
    protected $isInternal = false;

    /**
     * 允许上传的后缀名
     * @var array
     */
    protected $extension = [];

    /**
     * 构造函数
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * 设置上传的文件信息
     * @param string|UploadFile $file 表单字段名或上传文件对象
     * @param array $extension 额外允许的文件后缀
     * @return void
     * @throws Exception
     */
    public function setUploadFile(string|UploadFile $file, array $extension = [])
    {
        // 接收上传的文件
        if ($file instanceof UploadFile) {
            $this->file = $file;
        } else {
            $this->file = request()->file($file);
        }
        if (empty($this->file)) {
            throw new Exception('未找到上传文件的信息');
        }

        // 校验上传文件后缀
        $limit = array_merge(
            config('project.file_image', []),
            config('project.file_video', []),
            config('project.file_file', [])
        );
        // 设置外部传入的文件后缀
        if (!empty($extension)) {
            $limit = array_merge($limit, $extension);
        }
        $ext = strtolower($this->file->getUploadExtension());
        if (!in_array($ext, $limit) && $limit) {
            throw new Exception('不允许上传' . $ext . '后缀文件');
        }
        // 真实文件路径
        $realPath = $this->file->getRealPath();
        // 获取文件MD5
        $fileName = md5_file($realPath);
        $fileName = md5("{$this->config['plugin']}_{$fileName}");
        // 文件信息
        $this->fileInfo = [
            'ext' => $ext,
            'size' => $this->file->getSize(),
            'mime' => $this->file->getUploadMimeType(),
            'name' => $this->file->getUploadName(),
            'md5' => $fileName,
            'realPath' => $realPath,
        ];
        // 生成保存文件名
        $fileName = "{$fileName}.{$this->fileInfo['ext']}";
        // 保存文件名
        $this->fileName = $fileName;
    }

    /**
     * 设置上传的文件信息（通过真实文件路径）
     * @param string $filePath
     */
    public function setUploadFileByReal($filePath)
    {
        // 设置为系统内部上传
        $this->isInternal = true;
        // 文件信息
        $this->fileInfo = [
            'name' => basename($filePath),
            'size' => filesize($filePath),
            'tmp_name' => $filePath,
            'error' => 0,
        ];
        // 生成保存文件名
        $this->fileName = $this->buildSaveName();
    }

    /**
     * 抓取网络资源
     * @param string $url 网络资源地址
     * @param string $key 上传文件的表单名称
     * @return mixed
     */
    abstract public function fetch(string $url, $key);

    /**
     * 执行文件上传
     * @param string $save_dir 保存路径
     * @param array|null $callback 回调参数
     * @return mixed
     */
    abstract public function upload(string $save_dir, ?array $callback);

    /**
     * 检测文件是否存在
     * @param string $filePath 文件路径
     * @return bool
     */
    abstract public function exist(string $filePath);

    /**
     * 获取文件URL
     * @param string $filePath
     * @return string
     */
    abstract public function url(string $filePath);

    /**
     * 获取签名URL
     * @param string $filePath
     * @return string
     */
    abstract public function getSignUrl(string $filePath);

    /**
     * 获取服务域名
     * @return string
     */
    abstract public function domain();

    /**
     * 删除文件
     * @param string $fileName
     * @return mixed
     */
    abstract public function delete(string $fileName);

    /**
     * 返回上传后文件名
     * @return mixed
     */
    abstract public function getFileName();

    /**
     * 设置上传后的文件名
     * @param string $fileName
     * @return mixed
     */
    abstract public function setFileName(string $fileName);

    /**
     * 返回文件信息
     * @return array
     */
    public function getFileInfo()
    {
        return $this->fileInfo;
    }

    /**
     * 返回文件真实路径
     * @return string
     */
    protected function getRealPath()
    {
        return $this->fileInfo['realPath'] ?? $this->fileInfo['tmp_name'] ?? '';
    }

    /**
     * 返回错误信息
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 格式化URL地址
     * @param string $domain 域名
     * @param string $uri 路径
     * @return string
     */
    protected function format(string $domain, string $uri)
    {
        // 处理域名
        $domainLen = strlen($domain);
        $domainRight = substr($domain, $domainLen - 1, 1);
        if ('/' == $domainRight) {
            $domain = substr_replace($domain, '', $domainLen - 1, 1);
        }

        // 处理uri
        $uriLeft = substr($uri, 0, 1);
        if ('/' == $uriLeft) {
            $uri = substr_replace($uri, '', 0, 1);
        }

        return trim($domain) . '/' . trim($uri);
    }

    /**
     * 生成保存文件名
     * @return string
     */
    private function buildSaveName()
    {
        // 要上传文件的真实路径
        $realPath = $this->getRealPath();
        // 扩展名
        $ext = pathinfo($this->getFileInfo()['name'], PATHINFO_EXTENSION);
        // 自动生成文件名
        return date('YmdHis') . substr(md5($realPath), 0, 5)
            . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT) . ".{$ext}";
    }
}