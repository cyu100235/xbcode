<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbUpload\api;

use plugin\xbCode\api\ConfigApi;
use plugin\xbUpload\service\Driver;

/**
 * 分片上传处理
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class UploadChunk
{
    /**
     * 上传适配器
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $adapter = 'local';

    /**
     * 上传配置
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $config = [];

    /**
     * 储存目录
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $dir = '';

    /**
     * 上传驱动
     * @var Driver
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected Driver $driver;

    /**
     * 构造函数
     * @param string $adapter 上传适配器
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function __construct(string $adapter = '')
    {
        if (empty($adapter)) {
            $adapter = ConfigApi::make('upload')->get('active', 'local');
        }
        $config = EngineApi::make()->getConfig($adapter);
        $this->adapter = $adapter;
        $this->config = $config;
        $this->driver = Driver::make($config);
    }

    /**
     * 创建实例
     * @param string $adapter
     * @return UploadChunk
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function make(string $adapter = '')
    {
        $class = new static($adapter);
        return $class;
    }

    /**
     * 设置上传适配器
     * @param string $adapter
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setAdapter(string $adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * 设置储存目录
     * @param string $dir 储存目录
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setDir(string $dir)
    {
        $this->dir = $dir;
        return $this;
    }

    /**
     * 开始上传
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function start()
    {
        $sessionId = request()->sessionId();
        $name = request()->post('name');
        // 1.生成上传ID（唯一标识）
        $uploadId = md5("{$sessionId}_{$name}");
        // 2.分片目录（放在项目 runtime 下，避免占用系统 Temp 且便于统一清理）
        $chunkPath = $this->chunkDir($uploadId);
        /**
         * 需要返回以下参数
         * uploadId 这次上传的唯一ID。
         * key 后端生成的分片标识，仅返回标识符而非服务器路径（后续请求也只认 uploadId）
         * partList 已上传的分片列表，用于断点续传
         */
        // 扫描已上传的分片，用于断点续传
        $partList = [];
        if (is_dir($chunkPath)) {
            foreach (scandir($chunkPath) as $file) {
                if (!str_ends_with($file, '.tmp')) {
                    continue;
                }
                $eTag = substr($file, 0, -4);
                $partNumber = (int) explode('_', $eTag)[0];
                $partList[] = [
                    'partNumber' => $partNumber,
                    'eTag' => $eTag,
                ];
            }
        }
        return [
            'uploadId' => $uploadId,
            'key' => $uploadId,
            'partList' => $partList,
        ];
    }

    /**
     * 上传分片
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function chunk()
    {
        // 分块文件路径：优先由 uploadId 推导，兼容只传 key 的客户端
        $dirPath = $this->resolveChunkDir();
        // 分块编号
        $partNumber = (int) request()->post('partNumber');
        // 分块大小
        $partSize = (int) request()->post('partSize');
        // 文件内容
        $file = request()->file('file');
        // 检测目录不存在则创建
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0777, true);
        }
        // 生成eTag
        $uploadId = basename($dirPath);
        $tag = md5("{$uploadId}_{$partSize}");
        $tag = "{$partNumber}_{$tag}";
        // 分片文件地址
        $chunkPath = "{$dirPath}/{$tag}.tmp";
        if (!file_exists($chunkPath)) {
            // 保存文件
            $file->move($chunkPath);
        }
        // eTag 通常为文件的内容戳（MD5）
        return [
            'eTag' => $tag,
        ];
    }

    /**
     * 完成上传
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function finish()
    {
        // 分块文件路径：优先由 uploadId 推导，兼容只传 key 的客户端
        $dirPath = $this->resolveChunkDir();
        // 文件名（仅保留文件名部分，避免路径穿越）
        $filename = basename((string) request()->post('filename'));
        if ($filename === '' || $filename === '.' || $filename === '..') {
            throw new \Exception('文件名称不正确');
        }
        // 分块列表
        $partList = request()->post('partList');
        if (!is_array($partList) || empty($partList)) {
            throw new \Exception('缺少分片数据');
        }
        // 最终生成文件路径
        $finalFilePath = "{$dirPath}/{$filename}";
        // 创建最终生成文件
        $finalFile = fopen($finalFilePath, 'wb');
        if (!$finalFile) {
            throw new \Exception('无法创建临时文件');
        }
        try {
            foreach ($partList as $value) {
                // 分片文件名只允许由 eTag 规则生成的字符，防止越级读取
                $eTag = (string) ($value['eTag'] ?? '');
                if ($eTag === '' || !preg_match('/^[0-9]+_[A-Za-z0-9]+$/', $eTag)) {
                    continue;
                }
                $chunkPath = "{$dirPath}/{$eTag}.tmp";
                if (!is_file($chunkPath)) {
                    continue;
                }
                $chunkFile = fopen($chunkPath, 'rb');
                if (!$chunkFile) {
                    continue;
                }
                while (!feof($chunkFile)) {
                    $chunk = fread($chunkFile, 8192);
                    if ($chunk === false) {
                        break;
                    }
                    fwrite($finalFile, $chunk);
                }
                // 分片读取后立即关闭句柄，避免 Windows 下句柄占用导致后续删除失败
                fclose($chunkFile);
            }
        } finally {
            // 合并结果必须落盘关闭，否则临时文件同样会被句柄占用
            fclose($finalFile);
        }
        try {
            // 执行文件上传
            $class = UploadApi::make($this->adapter);
            if ($this->dir !== '') {
                $class->setSaveDir($this->dir);
            }
            $result = $class->uploadFilePath($finalFilePath);
        } finally {
            // 上传已完成，临时文件清理失败（如 Windows 句柄延迟释放）不应影响上传结果
            $this->cleanup($dirPath, $filename, $partList);
        }
        // 返回文件URL
        return [
            'url' => $result['url'],
            'value' => $result['url'],
        ];
    }

    /**
     * 分片临时目录根路径（放在项目 runtime 下，避免占用系统临时目录）
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function tempDir(): string
    {
        $path = runtime_path() . '/upload_chunk';
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        return $path;
    }

    /**
     * 获取指定上传ID的分片目录
     * @param string $uploadId 上传ID
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function chunkDir(string $uploadId): string
    {
        return $this->tempDir() . "/{$uploadId}";
    }

    /**
     * 解析本次请求对应的分片目录
     *
     * 客户端只传标识符，由服务端拼接到固定的分片根目录，避免把服务器路径下发给前端，
     * 也杜绝客户端传入任意路径造成的目录穿越：
     * - amis input-file 传 uploadId；
     * - 后台附件弹窗传 key（其值即 uploadId）。
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function resolveChunkDir(): string
    {
        $uploadId = (string) request()->post('uploadId');
        if (!preg_match('/^[a-f0-9]{32}$/', $uploadId)) {
            // 回退取 key：key 同样只接受 32 位标识符
            $uploadId = (string) request()->post('key');
        }
        if (!preg_match('/^[a-f0-9]{32}$/', $uploadId)) {
            throw new \Exception('上传标识不正确');
        }
        return $this->chunkDir($uploadId);
    }

    /**
     * 清理分片与合并产生的临时文件
     *
     * 清理属于收尾动作，任何失败（如 Windows 下句柄延迟释放导致 Permission denied）
     * 都只记录日志，不抛出异常，避免把已成功的上传误判为失败。
     * @param string $dirPath 分片目录
     * @param string $filename 合并后的文件名
     * @param array $partList 分片列表
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function cleanup(string $dirPath, string $filename, array $partList): void
    {
        $files = ["{$dirPath}/{$filename}"];
        foreach ($partList as $value) {
            $eTag = (string) ($value['eTag'] ?? '');
            if ($eTag !== '') {
                $files[] = "{$dirPath}/{$eTag}.tmp";
            }
        }
        foreach ($files as $file) {
            if (!is_file($file)) {
                continue;
            }
            // 句柄可能尚未被系统释放，重试几次
            for ($i = 0; $i < 3; $i++) {
                if (@unlink($file)) {
                    continue 2;
                }
                usleep(100000);
            }
            \support\Log::warning("分片临时文件清理失败：{$file}");
        }
        if (is_dir($dirPath)) {
            @rmdir($dirPath);
        }
    }
}