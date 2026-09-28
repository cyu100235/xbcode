<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbUploadCos\engine;

use Exception;
use Qcloud\Cos\Client;
use plugin\xbAdmin\service\Server;

/**
 * 腾讯云存储引擎(COS)
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class QcloudServer extends Server
{
    /**
     * COS控制类
     * @var Client
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private $cosClient;

    /**
     * 构造方法
     * @param array $config
     * @throws Exception
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        if (empty($config['region'])) {
            throw new Exception('请先配置腾讯云储存地区');
        }
        if (empty($config['secret_id'])) {
            throw new Exception('请先配置腾讯云secretId');
        }
        if (empty($config['secret_key'])) {
            throw new Exception('请先配置腾讯云secretKey');
        }
        // 创建COS控制类
        $this->createCosClient();
    }

    /**
     * 创建COS控制类
     * @throws Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function createCosClient()
    {
        if (!class_exists('Qcloud\Cos\Client')) {
            throw new Exception('请安装腾讯云COS SDK');
        }
        $this->cosClient = new Client([
            'region' => $this->config['region'],
            'credentials' => [
                'secretId' => $this->config['secret_id'],
                'secretKey' => $this->config['secret_key'],
            ],
        ]);
    }

    /**
     * 上传文件到存储
     * @param string $save_dir 保存路径
     * @param array|null $callback 回调参数
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function upload(string $save_dir, ?array $callback)
    {
        $options = [
            'Bucket' => $this->config['bucket'],
            'Key' => $save_dir . '/' . $this->fileName,
            'Body' => fopen($this->getRealPath(), 'rb'),
        ];
        if ($callback) {
            $options = array_merge($options, $callback);
        }
        $result = $this->cosClient->putObject($options);
        $data = $result->toArray();
        return [
            'url' => $data['Location'],
            'key' => $data['Key'],
        ];
    }

    /**
     * 检查文件是否存在
     * @param string $filePath
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function exist(string $filePath)
    {
        return $this->cosClient->doesObjectExist($this->config['bucket'], $filePath);
    }

    /**
     * 获取文件URL
     * @param string $filePath 文件路径
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function url(string $filePath)
    {
        return $this->format($this->config['domain'], $filePath);
    }

    /**
     * 获取签名URL
     * @param string $filePath
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getSignUrl(string $filePath)
    {
        return $this->cosClient->getObjectUrl($this->config['bucket'], $filePath, 3600);
    }

    /**
     * 获取服务域名
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function domain()
    {
        return $this->config['domain'];
    }

    /**
     * 抓取远程资源(最大支持上传5G文件)
     * @param mixed $url
     * @param mixed $key
     * @return bool|array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function fetch($url, $key = null)
    {
        $result = $this->cosClient->putObject([
            'Bucket' => $this->config['bucket'],
            'Key' => $key,
            'Body' => fopen($url, 'rb'),
        ]);
        return [
            'url' => $result['Location'],
            'key' => $result['Key'],
        ];
    }

    /**
     * 删除文件
     * @param mixed $fileName
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function delete($fileName)
    {
        $this->cosClient->deleteObject([
            'Bucket' => $this->config['bucket'],
            'Key' => $fileName,
        ]);
    }

    /**
     * 设置上传后的文件名
     * @param string $fileName
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setFileName(string $fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * 返回文件路径
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getFileName()
    {
        return $this->fileName;
    }
}