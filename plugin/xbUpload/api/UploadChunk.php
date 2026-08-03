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
        $this->driver = new Driver($config);
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
        // 2.临时目录
        $tempDir = sys_get_temp_dir();
        // 3.分块文件地址
        $chunkPath = "{$tempDir}/{$uploadId}";
        /**
         * 需要返回以下参数
         * uploadId 这次上传的唯一ID。
         * key 有点类似 uploadId，可有可无，用来记录后端文件存储路径
         */
        return [
            'uploadId' => $uploadId,
            'key' => $chunkPath,
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
        // 分片ID
        $uploadId = request()->post('uploadId');
        // 分块文件路径
        $dirPath = request()->post('key');
        // 分块编号
        $partNumber = request()->post('partNumber');
        // 分块大小
        $partSize = request()->post('partSize');
        // 文件内容
        $file = request()->file('file');
        // 检测目录不存在则创建
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0777, true);
        }
        // 生成eTag
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
        // 分块文件路径
        $dirPath = request()->post('key');
        // 文件名
        $filename = request()->post('filename');
        // 分块列表
        $partList = request()->post('partList');
        // 最终生成文件路径
        $finalFilePath = "{$dirPath}/{$filename}";
        // 创建最终生成文件
        $finalFile = fopen($finalFilePath, 'wb');
        foreach ($partList as $value) {
            $chunkPath = "{$dirPath}/{$value['eTag']}.tmp";
            $chunkFile = fopen($chunkPath, 'rb');
            while ($chunk = fread($chunkFile, 8192)) {
                fwrite($finalFile, $chunk);
            }
            fclose($chunkFile);
        }
        // 分片文件合并
        fclose($finalFile);
        // 删除分块文件
        foreach ($partList as $value) {
            $chunkPath = "{$dirPath}/{$value['eTag']}.tmp";
            unlink($chunkPath);
        }
        // 执行文件上传
        $result = UploadApi::make()->uploadFilePath($finalFilePath);
        // 删除临时文件
        if (file_exists($finalFilePath)) {
            unlink($finalFilePath);
        }
        // 删除目录
        if (is_dir($dirPath)) {
            rmdir($dirPath);
        }
        // 返回文件URL
        return [
            'url' => $result['url'],
            'value' => $result['url'],
        ];
    }
}