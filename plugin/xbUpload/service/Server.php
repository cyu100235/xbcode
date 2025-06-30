<?php
namespace plugin\xbUpload\service;

use think\Exception;
use Webman\Http\UploadFile;

/**
 * 存储引擎抽象类
 * Class server
 * @package app\common\library\storage\drivers
 */
abstract class Server
{
    /**
     * 上传配置
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
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
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    
    /**
     * 设置上传的文件信息
     * @param string $name
     * @param array $extension
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function setUploadFile(string $name,array $extension = [])
    {
        // 接收上传的文件
        $this->file = request()->file($name);
        if (empty($this->file)) {
            throw new Exception('未找到上传文件的信息');
        }

        // 校验上传文件后缀
        $limit = array_merge(
            config('project.file_image',[]),
            config('project.file_video',[]),
            config('project.file_file',[])
        );
        // 设置外部传入的文件后缀
        if (!empty($extension)) {
            $limit = array_merge($limit, $extension);
        }
        $ext = strtolower($this->file->getUploadExtension());
        if (!in_array($ext, $limit) && $limit) {
            throw new Exception('不允许上传' . $ext . '后缀文件');
        }

        // 文件信息
        $this->fileInfo = [
            'ext'      => $ext,
            'size'     => $this->file->getSize(),
            'mime'     => $this->file->getUploadMimeType(),
            'name'     => $this->file->getUploadName(),
            'realPath' => $this->file->getRealPath(),
        ];
        // 获取文件MD5
        $fileName = md5_file($this->fileInfo['realPath']);
        // 生成保存文件名
        $fileName = "{$fileName}.{$this->fileInfo['ext']}";
        // 保存文件名
        $this->fileName = $fileName;
    }

    /**
     * 设置上传的文件信息
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
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    abstract protected function fetch(string $url, string $key);

    /**
     * 执行文件上传
     * @param string $save_dir 保存路径
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    abstract protected function upload(string $save_dir);

    /**
     * 文件删除
     * @param $fileName
     * @return mixed
     */
    abstract protected function delete($fileName);

    /**
     * 返回上传后文件路径
     * @return mixed
     */
    abstract public function getFileName();

    /**
     * 返回文件信息
     * @return mixed
     */
    public function getFileInfo()
    {
        return $this->fileInfo;
    }

    protected function getRealPath()
    {
        return $this->fileInfo['realPath'];
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
     * 生成保存文件名
     */
    private function buildSaveName()
    {
        // 要上传图片的本地路径
        $realPath = $this->getRealPath();
        // 扩展名
        $ext = pathinfo($this->getFileInfo()['name'], PATHINFO_EXTENSION);
        // 自动生成文件名
        return date('YmdHis') . substr(md5($realPath), 0, 5)
            . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT) . ".{$ext}";
    }
}
