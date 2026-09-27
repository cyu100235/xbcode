<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\api;

use Exception;
use Webman\Http\UploadFile;
use plugin\xbAdmin\service\Driver;
use plugin\xbAdmin\app\model\Config;
use plugin\xbAdmin\app\model\Upload;
use plugin\xbAdmin\enum\UploadExtEnum;

/**
 * 附件接口类
 *
 * 负责接收上传文件、按 md5 去重、落盘并写入附件记录，返回统一的附件信息。
 */
class UploadApi
{
    /**
     * 存取引擎驱动
     * @var Driver
     */
    protected Driver $driver;

    /**
     * 当前使用的引擎标识
     * @var string
     */
    protected string $adapter;

    /**
     * 引擎配置
     * @var array
     */
    protected array $config;

    /**
     * 上传用户ID
     * @var int
     */
    protected int $uid = 0;

    /**
     * 是否写入附件记录
     * @var bool
     */
    protected bool $saveRecord = true;

    /**
     * 保存目录
     * @var string
     */
    protected string $saveDir = '';

    /**
     * 保存文件名
     * @var string
     */
    protected string $fileName = '';

    /**
     * 构造函数
     * @param string $adapter 引擎标识，缺省取后台配置的默认引擎
     */
    protected function __construct(string $adapter = '')
    {
        if (empty($adapter)) {
            $values = Config::groupValues('xbAdmin', 'upload');
            $adapter = (string) ($values['active'] ?? 'local');
        }
        $this->adapter = $adapter;
        $this->config = EngineApi::make()->getConfig($adapter);
        $this->driver = Driver::make($this->config, $adapter);
    }

    /**
     * 创建实例
     * @param string $adapter 引擎标识
     * @return UploadApi
     */
    public static function make(string $adapter = ''): UploadApi
    {
        return new static($adapter);
    }

    /**
     * 上传文件（从当前请求中取文件）
     * @param string $name 表单字段名
     * @return array
     * @throws Exception
     */
    public function upload(string $name = 'file'): array
    {
        $file = request()->file($name);
        if (!$file instanceof UploadFile) {
            throw new Exception('上传文件不存在');
        }
        return $this->uploadFile($file);
    }

    /**
     * 执行文件上传
     * @param UploadFile $file 上传文件对象
     * @return array
     * @throws Exception
     */
    public function uploadFile(UploadFile $file): array
    {
        // 1.设置上传文件并校验
        $this->driver->setUploadFile($file);
        // 2.获取文件信息
        $fileInfo = $this->driver->getFileInfo();
        // 3.文件指纹
        $md5 = $fileInfo['md5'] ?? '';
        if (empty($md5)) {
            throw new Exception('上传文件MD5值为空');
        }
        // 4.同一用户同一引擎下文件已存在时直接复用
        $model = Upload::where([
            'md5' => $md5,
            'uid' => $this->uid,
            'adapter' => $this->adapter,
        ])->find();
        if ($model && $this->driver->exist($model['uri'])) {
            $model->update_at = date('Y-m-d H:i:s');
            $model->save();
            return $this->getResult($model->toArray());
        }
        // 5.文件后缀
        $ext = $fileInfo['ext'] ?? '';
        if (empty($this->fileName)) {
            $this->fileName = $this->driver->getFileName();
        }
        // 6.保存目录
        if (empty($this->saveDir)) {
            $this->saveDir = $this->getUploadPath($ext);
        }
        // 7.执行上传
        $this->driver->setFileName($this->fileName);
        $this->driver->upload($this->saveDir);
        // 8.组装附件信息
        $data = [
            'uid' => $this->uid,
            'title' => $fileInfo['name'],
            'name' => $fileInfo['name'],
            'md5' => $md5,
            'size' => $fileInfo['size'],
            'format' => $ext,
            'adapter' => $this->adapter,
            'uri' => "{$this->saveDir}/{$this->fileName}",
        ];
        if ($this->saveRecord) {
            Upload::create($data);
        }
        return $this->getResult($data);
    }

    /**
     * 下载远程文件并上传
     * @param string $url 远程地址
     * @return array
     * @throws Exception
     */
    public function download(string $url): array
    {
        if (empty($url)) {
            throw new Exception('下载地址不能为空');
        }
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new Exception('下载地址格式不正确');
        }
        $saveDir = runtime_path() . '/temp';
        if (!is_dir($saveDir)) {
            mkdir($saveDir, 0777, true);
        }
        if (empty($this->fileName)) {
            $this->fileName = basename($url);
        }
        $filePath = $saveDir . '/' . $this->fileName;
        $resource = fopen($url, 'r');
        if ($resource === false) {
            throw new Exception('下载文件失败');
        }
        file_put_contents($filePath, $resource);
        return $this->uploadFilePath($filePath);
    }

    /**
     * 上传服务器本地文件
     * @param string $path 文件路径
     * @return array
     * @throws Exception
     */
    public function uploadFilePath(string $path): array
    {
        if (empty($path)) {
            throw new Exception('文件路径不能为空');
        }
        if (!is_file($path)) {
            throw new Exception('文件路径不存在');
        }
        $mimeType = mime_content_type($path) ?: '';
        $fileName = pathinfo($path, PATHINFO_BASENAME);
        $file = new UploadFile($path, $fileName, $mimeType, 0);
        return $this->uploadFile($file);
    }

    /**
     * 设置保存目录
     * @param string $saveDir 保存目录
     * @return static
     */
    public function setSaveDir(string $saveDir): static
    {
        $this->saveDir = $saveDir;
        return $this;
    }

    /**
     * 设置保存文件名
     * @param string $fileName 文件名
     * @return static
     */
    public function setFileName(string $fileName): static
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * 设置是否写入附件记录
     * @param bool $saveRecord 是否保存记录
     * @return static
     */
    public function setSaveRecord(bool $saveRecord): static
    {
        $this->saveRecord = $saveRecord;
        return $this;
    }

    /**
     * 设置上传用户ID
     * @param int $uid 用户ID
     * @return static
     */
    public function setUid(int $uid): static
    {
        $this->uid = $uid;
        return $this;
    }

    /**
     * 设置引擎标识
     * @param string $adapter 引擎标识
     * @return static
     */
    public function setAdapter(string $adapter): static
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * 获取引擎标识
     * @return string
     */
    public function getAdapter(): string
    {
        return $this->adapter;
    }

    /**
     * 获取引擎驱动
     * @return Driver
     */
    public function getDriver(): Driver
    {
        return $this->driver;
    }

    /**
     * 组装返回的附件信息
     * @param array $result 附件数据
     * @return array
     */
    protected function getResult(array $result): array
    {
        $url = Files::make()->url((string) $result['uri']);
        return [
            'uid' => $this->uid,
            'title' => $result['name'],
            'name' => $result['name'],
            'md5' => $result['md5'],
            'size' => $result['size'],
            'format' => $result['format'],
            'adapter' => $this->adapter,
            'uri' => $result['uri'],
            'url' => $url,
            'value' => $url,
            'link' => $url,
        ];
    }

    /**
     * 按文件后缀归类保存目录
     * @param string $extension 文件后缀
     * @return string
     */
    protected function getUploadPath(string $extension): string
    {
        return 'attachment/' . $this->getDictDirName($extension) . '/' . date('Ymd');
    }

    /**
     * 获取后缀所属的分类目录名
     * @param string $extension 文件后缀
     * @return string
     */
    protected function getDictDirName(string $extension): string
    {
        foreach (UploadExtEnum::toArray() as $value) {
            $format = array_filter(explode(',', (string) ($value['ext'] ?? '')));
            if ($extension !== '' && in_array($extension, $format, true)) {
                return (string) ($value['value'] ?? 'other');
            }
        }
        return 'other';
    }
}