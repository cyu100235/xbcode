<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\api;

use Exception;
use plugin\xbAdmin\service\Driver;
use plugin\xbAdmin\app\model\Upload;

/**
 * 附件服务类
 *
 * 对外的附件统一入口，负责按 uri 定位其所属引擎并转发到对应驱动。
 */
class Files
{
    /**
     * 创建实例
     * @return Files
     */
    public static function make(): Files
    {
        return new static;
    }

    /**
     * 获取访问地址
     * @param string|array $uri 文件路径
     * @param string|null $adapter 引擎标识，缺省按附件记录推断
     * @param mixed $default 空值时的默认返回
     * @return mixed
     */
    public function url(string|array $uri, ?string $adapter = null, mixed $default = '')
    {
        if (is_array($uri)) {
            $data = [];
            foreach ($uri as $key => $value) {
                $data[$key] = static::url($value, $adapter, $default);
            }
            return $data;
        }
        if (empty($uri)) {
            return $default;
        }
        if (str_contains($uri, 'http://') || str_contains($uri, 'https://')) {
            return $uri;
        }
        if (empty($adapter)) {
            $adapter = Upload::where('uri', $uri)->value('adapter', 'local');
        }
        $config = EngineApi::make()->getConfig($adapter);
        return Driver::make($config, $adapter)->url($uri);
    }

    /**
     * 获取签名下载地址
     * @param string|array $uri 文件路径
     * @param string|null $adapter 引擎标识，缺省按附件记录推断
     * @param mixed $default 空值时的默认返回
     * @return mixed
     * @throws Exception
     */
    public function getSignUrl(string|array $uri, ?string $adapter = null, mixed $default = '')
    {
        if (is_array($uri)) {
            $data = [];
            foreach ($uri as $key => $value) {
                $data[$key] = static::getSignUrl($value, $adapter, $default);
            }
            return $data;
        }
        if (empty($uri)) {
            return $default;
        }
        if (empty($adapter)) {
            $adapter = Upload::where('uri', $uri)->value('adapter', 'local');
        }
        $config = EngineApi::make()->getConfig($adapter);
        $driver = Driver::make($config, $adapter);
        if (!method_exists($driver, 'getSignUrl')) {
            throw new Exception('当前储存引擎不支持获取签名下载地址');
        }
        return $driver->getSignUrl($uri);
    }

    /**
     * 检测文件是否存在
     * @param string|array $uri 文件路径
     * @param string|null $adapter 引擎标识，缺省按附件记录推断
     * @return bool
     */
    public function exist(string|array $uri, ?string $adapter = null): bool
    {
        if (is_array($uri)) {
            foreach ($uri as $value) {
                if (!static::exist($value, $adapter)) {
                    return false;
                }
            }
            return true;
        }
        if (empty($adapter)) {
            $adapter = Upload::where('uri', $uri)->value('adapter', 'local');
        }
        $config = EngineApi::make()->getConfig($adapter);
        return Driver::make($config, $adapter)->exist($uri);
    }

    /**
     * 从访问地址还原文件路径
     * @param mixed $url 访问地址
     * @param mixed $default 空值时的默认返回
     * @return mixed
     * @throws Exception
     */
    public function path(mixed $url, mixed $default = '')
    {
        if (empty($url)) {
            return $default;
        }
        if (is_array($url)) {
            $data = [];
            foreach ($url as $value) {
                $data[] = static::path($value, $default);
            }
            return $data;
        }
        if (!is_valid_url((string) $url)) {
            throw new Exception('URL地址不合法');
        }
        return ltrim((string) parse_url((string) $url, PHP_URL_PATH), '/');
    }

    /**
     * 删除文件（同时删除附件记录）
     * @param array|string $uri 文件路径
     * @param string $adapter 引擎标识，缺省按附件记录推断
     * @return void
     * @throws Exception
     */
    public function delete(array|string $uri, string $adapter = ''): void
    {
        if (is_array($uri)) {
            foreach ($uri as $value) {
                $this->delete($value, $adapter);
            }
            return;
        }
        if (empty($adapter)) {
            $adapter = Upload::where('uri', $uri)->value('adapter', 'local');
        }
        $model = Upload::where(['uri' => $uri, 'adapter' => $adapter])->find();
        if ($model) {
            $model->delete();
        }
        $config = EngineApi::make()->getConfig($adapter);
        Driver::make($config, $adapter)->delete($uri);
    }
}