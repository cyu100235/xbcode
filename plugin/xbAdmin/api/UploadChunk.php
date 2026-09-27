<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\api;

use Exception;
use plugin\xbAdmin\service\Driver;
use plugin\xbAdmin\app\model\Config;

/**
 * 分片上传处理
 *
 * 大文件由前端切片后逐个上传，后端只负责暂存分片并返回 eTag，
 * 全部上传完成后由 finish 合并分片再走常规上传流程。
 */
class UploadChunk
{
    /**
     * 当前使用的引擎标识
     * @var string
     */
    protected string $adapter = 'local';

    /**
     * 引擎配置
     * @var array
     */
    protected array $config = [];

    /**
     * 存取引擎驱动
     * @var Driver
     */
    protected Driver $driver;

    /**
     * 构造函数
     * @param string $adapter 引擎标识，缺省取后台配置的默认引擎
     */
    public function __construct(string $adapter = '')
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
     * @return UploadChunk
     */
    public static function make(string $adapter = ''): UploadChunk
    {
        return new static($adapter);
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
     * 开始上传：生成上传ID与分片暂存目录
     * @return array{uploadId:string,key:string}
     */
    public function start(): array
    {
        $sessionId = request()->sessionId();
        $name = (string) request()->post('name', '');
        $uploadId = md5("{$sessionId}_{$name}");
        $chunkPath = sys_get_temp_dir() . "/{$uploadId}";
        return [
            'uploadId' => $uploadId,
            'key' => $chunkPath,
        ];
    }

    /**
     * 上传分片：保存单个分片并返回 eTag
     * @return array{eTag:string}
     * @throws Exception
     */
    public function chunk(): array
    {
        $uploadId = (string) request()->post('uploadId', '');
        $dirPath = (string) request()->post('key', '');
        $partNumber = (string) request()->post('partNumber', '');
        $partSize = (string) request()->post('partSize', '');
        $file = request()->file('file');
        if (empty($uploadId) || empty($dirPath)) {
            throw new Exception('分片上传参数不完整');
        }
        if (empty($file)) {
            throw new Exception('分片文件不存在');
        }
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0777, true);
        }
        $tag = "{$partNumber}_" . md5("{$uploadId}_{$partSize}");
        $chunkPath = "{$dirPath}/{$tag}.tmp";
        if (!file_exists($chunkPath)) {
            $file->move($chunkPath);
        }
        return [
            'eTag' => $tag,
        ];
    }

    /**
     * 完成上传：合并分片并执行上传
     * @return array{url:string,value:string}
     * @throws Exception
     */
    public function finish(): array
    {
        $dirPath = (string) request()->post('key', '');
        $filename = (string) request()->post('filename', '');
        $partList = request()->post('partList', []);
        if (is_string($partList)) {
            $partList = json_decode($partList, true) ?: [];
        }
        if (empty($dirPath) || empty($filename) || empty($partList)) {
            throw new Exception('分片合并参数不完整');
        }
        $finalFilePath = "{$dirPath}/{$filename}";
        // 逐个分片追加写入最终文件
        $finalFile = fopen($finalFilePath, 'wb');
        if ($finalFile === false) {
            throw new Exception('创建合并文件失败');
        }
        foreach ($partList as $value) {
            $chunkPath = "{$dirPath}/{$value['eTag']}.tmp";
            if (!file_exists($chunkPath)) {
                continue;
            }
            $chunkFile = fopen($chunkPath, 'rb');
            while ($chunk = fread($chunkFile, 8192)) {
                fwrite($finalFile, $chunk);
            }
            fclose($chunkFile);
        }
        fclose($finalFile);
        // 清理分片
        foreach ($partList as $value) {
            $chunkPath = "{$dirPath}/{$value['eTag']}.tmp";
            if (file_exists($chunkPath)) {
                unlink($chunkPath);
            }
        }
        // 合并完成后走常规上传流程
        $result = UploadApi::make($this->adapter)->uploadFilePath($finalFilePath);
        // 清理临时文件与目录
        if (file_exists($finalFilePath)) {
            unlink($finalFilePath);
        }
        if (is_dir($dirPath)) {
            rmdir($dirPath);
        }
        return [
            'url' => $result['url'],
            'value' => $result['url'],
        ];
    }
}