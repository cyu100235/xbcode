<?php

namespace xbcode\providers;

use Exception;
use think\facade\Db;

/**
 * 附件服务类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class FileProvider
{
    /**
     * 获取URL地址
     * @param mixed $uri
     * @param string $adapter
     * @param mixed $default
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function url(mixed $uri, string $adapter = '', $default = null)
    {
        // 数组类型URL
        if (is_array($uri)) {
            $data = [];
            foreach ($uri as $key => $value) {
                $data[$key] = self::url($value, $adapter, $default);
            }
            return $data;
        }
        if (empty($uri)) {
            return $default;
        }
        // 检测是否为URL地址
        if (strstr($uri, 'http://'))  return $uri;
        if (strstr($uri, 'https://')) return $uri;
        // 获取文件所属驱动
        if (empty($adapter)) {
            $adapter = Db::name('upload')->where('uri', $uri)->value('adapter', 'local');
        }
        if ($adapter === 'local') {
            $domain = '//'.request()->host();
        } else {
            $config = ConfigProvider::get('upload', $adapter, [], ['checked' => true]);
            $domain = $config['domain'] ?? '';
        }
        // 返回链接
        return self::format($domain, $uri);
    }

    /**
     * 获取附件路径
     * @param string $path
     * @param mixed $default
     * @return string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function path(mixed $url, mixed $default = '')
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
                $data[]   = ltrim($parseUrl['path'], '/');
            }
            return $data;
        }
        if (filter_var($url, FILTER_SANITIZE_URL) === false) {
            throw new Exception('URL地址不合法');
        }
        $parseUrl = parse_url($url);
        $data     = ltrim($parseUrl['path'], '/');
        return $data;
    }

    /**
     * 删除文件
     * @param array|string $uri
     * @param string $adapter
     * @throws \Exception
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function delete(array|string $uri, string $adapter = '')
    {
        if (is_array($uri)) {
            foreach ($uri as $value) {
                self::delete($value, $adapter);
            }
        } else {
            // 查询附件库记录
            $model = Db::name('upload')->where('uri', $uri)->find();
            if (!$model) {
                throw new Exception('文件路径参数错误');
            }
            // 检测文件是否存在
            $fullPath = public_path() . $uri;
            if (file_exists($fullPath)) {
                // 删除文件
                unlink($fullPath);
            }
            // 删除附件库记录
            if (!Db::name('upload')->where('id', $model['id'])->delete()) {
                throw new Exception('删除附件库记录失败');
            }
        }
    }

    /**
     * 格式化URL地址
     * @param mixed $domain
     * @param mixed $uri
     * @return string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function format($domain, $uri)
    {
        // 处理域名
        $domainLen = strlen($domain);
        $domainRight = substr($domain, $domainLen -1, 1);
        if ('/' == $domainRight) {
            $domain = substr_replace($domain,'',$domainLen -1, 1);
        }

        // 处理uri
        $uriLeft = substr($uri, 0, 1);
        if('/' == $uriLeft) {
            $uri = substr_replace($uri,'',0, 1);
        }

        return trim($domain) . '/' . trim($uri);
    }
}