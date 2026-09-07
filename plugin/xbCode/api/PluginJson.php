<?php

namespace plugin\xbCode\api;

use RuntimeException;

/**
 * plugin.json 信息读写
 *
 * 对外只暴露 get / set 两个方法，用法：
 *
 * PluginJson::get();                          // 取全部信息
 * PluginJson::get('version');                 // 取顶级字段
 * PluginJson::get('plugins.xbFoo.title');     // 点式路径取嵌套字段
 * PluginJson::set('version', '1.0.1');        // 修改字段并写回文件
 * PluginJson::set('plugins.xbFoo', [...]);    // 新增（覆盖）嵌套节点
 * PluginJson::set('plugins.xbFoo', null);     // 删除节点
 * PluginJson::set(null, $data);               // 整体覆盖文件内容
 *
 * 说明：固定读写插件根目录下的 plugin.json（../plugin.json）；
 *      读取结果按文件修改时间与大小缓存，常驻内存进程下不会每次读盘。
 */
class PluginJson
{

    /**
     * plugin.json 文件路径
     * @var string
     */
    protected static string $file = '';

    /**
     * 内容缓存
     * @var array{file:string, mtime:int, size:int, data:array}|null
     */
    protected static ?array $cache = null;

    /**
     * 读取 plugin.json 信息
     * @param string|null $name 字段名，支持点式路径（如 plugins.xbFoo.title）；留空返回全部信息
     * @param mixed $default 字段不存在时的默认值
     * @param string|null $file 指定要读取的 plugin.json 绝对路径；留空读取本插件（xbCode）根目录下的 plugin.json
     * @return mixed 全部信息返回 array（会自动注入插件目录名 name 字段），其余情况返回值或 $default
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function get(?string $name = null, mixed $default = null, ?string $file = null): mixed
    {
        $data = static::read($file);
        if ($name === null || $name === '') {
            // plugin.json 本身不存放插件标识，这里按文件所在目录注入 name（即插件目录名），便于上层识别插件
            $data['name'] = basename(dirname($file ?: static::file()));
            return $data;
        }
        return static::value($data, $name, $default);
    }

    /**
     * 修改 plugin.json 信息并写回文件
     * @param string|null $name 字段名，支持点式路径；留空表示用 $value 整体覆盖文件内容
     * @param mixed $value 字段值，传 null 表示删除该字段（中间节点会被保留）
     * @return bool 写入成功返回 true，写盘失败返回 false
     * @throws RuntimeException 文件内容非法 JSON、整体覆盖时传入非数组、内容编码失败
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function set(?string $name, mixed $value): bool
    {
        $data = static::read();
        if ($name === null || $name === '') {
            if (!is_array($value)) {
                throw new RuntimeException('整体覆盖 plugin.json 时，内容必须是数组');
            }
            $data = $value;
        } else {
            static::setValue($data, $name, $value);
        }
        return static::write($data);
    }

    /**
     * plugin.json 文件路径
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function file(): string
    {
        return static::$file ?: (static::$file = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'plugin.json');
    }

    /**
     * 读取文件内容（按修改时间 + 大小缓存，避免常驻进程反复读盘）
     * @param string|null $path 指定 plugin.json 绝对路径；留空读取本插件（xbCode）根目录下的 plugin.json
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function read(?string $path = null): array
    {
        $file = $path ?: static::file();
        clearstatcache(true, $file);
        $exists = is_file($file);
        $mtime = $exists ? (int)filemtime($file) : 0;
        $size = $exists ? (int)filesize($file) : 0;
        $cache = static::$cache;
        if ($cache && $cache['file'] === $file && $cache['mtime'] === $mtime && $cache['size'] === $size) {
            return $cache['data'];
        }
        $data = static::load($file);
        static::$cache = ['file' => $file, 'mtime' => $mtime, 'size' => $size, 'data' => $data];
        return $data;
    }

    /**
     * 解析文件内容
     * @param string $file 文件路径
     * @return array
     * @throws RuntimeException 内容不是合法的 JSON 对象
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function load(string $file): array
    {
        if (!is_file($file)) {
            return [];
        }
        $content = (string)file_get_contents($file);
        // 兼容带 BOM 的文件
        $content = ltrim($content, "\xEF\xBB\xBF");
        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException("plugin.json 内容不是合法的 JSON：{$file}");
        }
        return is_array($data) ? $data : [];
    }

    /**
     * 按字段名取值，优先匹配完整字段名，其次按点式路径向下取
     * @param array $data 全部数据
     * @param string $name 字段名
     * @param mixed $default 默认值
     * @return mixed
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function value(array $data, string $name, mixed $default): mixed
    {
        if (array_key_exists($name, $data)) {
            return $data[$name];
        }
        if (!str_contains($name, '.')) {
            return $default;
        }
        $value = $data;
        foreach (explode('.', $name) as $key) {
            if (!is_array($value) || !array_key_exists($key, $value)) {
                return $default;
            }
            $value = $value[$key];
        }
        return $value;
    }

    /**
     * 按点式路径写值，路径中缺失的节点自动创建为数组
     * @param array $data 全部数据（引用）
     * @param string $name 字段名
     * @param mixed $value 值，传 null 表示删除
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function setValue(array &$data, string $name, mixed $value): void
    {
        $keys = explode('.', $name);
        $last = array_pop($keys);
        $node = &$data;
        foreach ($keys as $key) {
            if (!isset($node[$key]) || !is_array($node[$key])) {
                $node[$key] = [];
            }
            $node = &$node[$key];
        }
        if ($value === null) {
            unset($node[$last]);
            return;
        }
        $node[$last] = $value;
    }

    /**
     * 写回文件
     * @param array $data 全部数据
     * @return bool
     * @throws RuntimeException 内容编码失败
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function write(array $data): bool
    {
        $json = json_encode(
            static::emptyAsObject($data),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
        );
        if ($json === false) {
            throw new RuntimeException('plugin.json 内容编码失败：' . json_last_error_msg());
        }
        $file = static::file();
        if (@file_put_contents($file, $json . PHP_EOL, LOCK_EX) === false) {
            return false;
        }
        clearstatcache(true, $file);
        static::$cache = [
            'file'  => $file,
            'mtime' => (int)filemtime($file),
            'size'  => (int)filesize($file),
            'data'  => $data,
        ];
        return true;
    }

    /**
     * 递归归一化：空数组输出为 {}，保证 "plugins": {} 之类的映射表不被写成 []
     * @param mixed $data
     * @return mixed
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function emptyAsObject(mixed $data): mixed
    {
        if (!is_array($data)) {
            return $data;
        }
        if ($data === []) {
            return new \stdClass();
        }
        $result = [];
        foreach ($data as $key => $value) {
            $result[$key] = static::emptyAsObject($value);
        }
        return $result;
    }

}
