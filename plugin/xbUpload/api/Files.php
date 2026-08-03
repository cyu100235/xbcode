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

use Exception;
use plugin\xbUpload\service\Driver;
use plugin\xbUpload\app\model\Upload;

/**
 * 附件服务类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class Files
{
    /**
     * 创建实例
     * @return Files
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function make()
    {
        $class = new static;
        return $class;
    }

    /**
     * 获取URL地址
     * @param mixed $uri 文件路径
     * @param string $adapter 上传适配器
     * @param mixed $default 默认值
     * @return mixed
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function url(string|array $uri, ?string $adapter = null, mixed $default = '')
    {
        // 数组类型URL
        if (is_array($uri)) {
            $data = [];
            foreach ($uri as $key => $value) {
                $data[$key] = static::url($value, $default);
            }
            return $data;
        }
        if (empty($uri)) {
            return $default;
        }
        // 检测是否为URL地址
        if (str_contains($uri, 'http://') || str_contains($uri, 'https://')) {
            return $uri;
        }
        // 设置上传适配器
        if (empty($adapter)) {
            $adapter = Upload::where('uri', $uri)->value('adapter', 'local');
        }
        $config = EngineApi::make()->getConfig($adapter);
        return Driver::make($config, $adapter)->url($uri);
    }

    /**
     * 获取签名下载地址
     * @param string|array $uri 文件路径
     * @param string $adapter 上传适配器
     * @param mixed $default 默认值
     * @return mixed
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function getSignUrl(string|array $uri, ?string $adapter = null, mixed $default = '')
    {
        if (is_array($uri)) {
            $data = [];
            foreach ($uri as $key => $value) {
                $data[$key] = static::getSignUrl($value, $default);
            }
            return $data;
        }
        // 设置上传适配器
        if (empty($adapter)) {
            $adapter = Upload::where('uri', $uri)->value('adapter', 'local');
        }
        $config = EngineApi::make()->getConfig($adapter);
        $driver = Driver::make($config, $adapter);
        if (!method_exists($driver, 'getSignUrl')) {
            throw new Exception('当前上传适配器不支持获取签名下载地址');
        }
        return $driver->getSignUrl($uri);
    }

    /**
     * 检测文件是否存在
     * @param string|array $uri
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function exist(string|array $uri, ?string $adapter = null)
    {
        if (is_array($uri)) {
            foreach ($uri as $value) {
                if (!self::exist($value)) {
                    return false;
                }
            }
            return true;
        }
        // 设置上传适配器
        if (empty($adapter)) {
            $adapter = Upload::where('uri', $uri)->value('adapter', 'local');
        }
        $config = EngineApi::make()->getConfig($adapter);
        return Driver::make($config)->exist($uri);
    }

    /**
     * 获取附件路径
     * @param string $url
     * @param mixed $default
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function path(mixed $url, mixed $default = '')
    {
        if (empty($url)) {
            return $default;
        }
        if (is_array($url) && count($url) > 0) {
            $data = [];
            if (count($url) === 1) {
                return self::path(current($url));
            }
            foreach ($url as $value) {
                if (filter_var($value, FILTER_SANITIZE_URL) === false) {
                    throw new Exception('URL地址不合法');
                }
                $parseUrl = parse_url($value);
                $data[] = ltrim($parseUrl['path'], '/');
            }
            return $data;
        }
        if (filter_var($url, FILTER_SANITIZE_URL) === false) {
            throw new Exception('URL地址不合法');
        }
        $parseUrl = parse_url($url);
        $data = ltrim($parseUrl['path'], '/');
        return $data;
    }

    /**
     * 删除文件
     * @param array|string $uri
     * @param string $adapter
     * @throws \Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function delete(array|string $uri, string $adapter = '')
    {
        if (is_array($uri)) {
            foreach ($uri as $value) {
                $this->delete($value, $adapter);
            }
        } else {
            if (empty($adapter)) {
                $adapter = Upload::where('uri', $uri)->value('adapter', 'local');
            }
            // 查询附件库记录
            $where = [
                'uri' => $uri,
                'adapter' => $adapter
            ];
            $model = Upload::where($where)->find();
            if ($model) {
                // 删除附件记录
                $model->delete();
            }
            // 执行删除操作
            $config = EngineApi::make()->getConfig($adapter);
            Driver::make($config, $adapter)->delete($uri);
        }
    }
}