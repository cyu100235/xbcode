<?php
/**
 * nginx.conf 伪静态配置读写（对外仅开放 get() / set()）
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */

namespace plugin\xbCode\api;

use RuntimeException;

/**
 * nginx.conf 读写器
 *
 * 对外只暴露 get / set 两个方法，固定读写项目根目录下的 nginx.conf，用法：
 *
 * Nginx::get();                      // 读取整份 nginx.conf 原文（string）
 * Nginx::get('port');                // 读取 proxy_pass 端口（int）
 * Nginx::get('proxy_buffering');     // 按指令名读取单行指令值（string）
 * Nginx::get('xxx', '默认值');        // 指令不存在时返回默认值
 *
 * Nginx::set('port', 39000);         // 修改 proxy_pass 端口并写回
 * Nginx::set('proxy_buffering', 'on');// 修改已有指令值（不存在时在顶层追加）
 * Nginx::set(null, $content);        // 用字符串整体覆盖文件
 *
 * 说明：写入采用「原文定位替换」，只改动目标指令，其余内容与格式原样保留；
 *      读取结果按文件修改时间与大小缓存，常驻内存进程下不会每次读盘。
 */
class Nginx
{
    /**
     * nginx.conf 文件路径（惰性解析后缓存）
     * @var string
     */
    protected static string $file = '';

    /**
     * 内容缓存
     * @var array{file:string, mtime:int, size:int, content:string}|null
     */
    protected static ?array $cache = null;

    /**
     * 读取 nginx.conf 内容
     * @param string|null $name    配置项：留空返回整份原文；'port' 返回 proxy_pass 端口；其余按指令名返回单行指令值
     * @param mixed       $default 配置项不存在时的默认值（仅在传入 $name 时生效）
     * @return mixed 无 $name 返回 string，'port' 返回 int，指令返回 string，未命中返回 $default
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function get(?string $name = null, mixed $default = null): mixed
    {
        $content = static::read();
        if ($name === null || $name === '') {
            return $content;
        }
        $value = ($name === 'port') ? static::port($content) : static::directive($content, $name);
        return $value ?? $default;
    }

    /**
     * 修改 nginx.conf 并写回文件
     * @param string|null $name  配置项：留空表示用 $value 整体覆盖文件；'port' 修改端口；其余按指令名修改（不存在时在顶层追加）
     * @param mixed       $value 配置值；整体覆盖时必须是字符串
     * @return bool 写入成功返回 true，写盘失败返回 false
     * @throws RuntimeException 整体覆盖传入非字符串、端口非法、不支持的指令、目标指令无法定位
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function set(?string $name, mixed $value): bool
    {
        // 整体覆盖
        if ($name === null || $name === '') {
            if (!is_string($value)) {
                throw new RuntimeException('整体覆盖 nginx.conf 时，内容必须是字符串');
            }
            return static::write($value);
        }
        $content = static::read();
        $new = ($name === 'port')
            ? static::setPort($content, $value)
            : static::setDirective($content, $name, $value);
        if ($new === null) {
            throw new RuntimeException("无法写入 nginx 配置项：{$name}");
        }
        return static::write($new);
    }

    /* ---------------------------- 内部能力（不对外暴露） ---------------------------- */

    /**
     * nginx.conf 文件路径（默认 BASE_PATH/nginx.conf）
     * @return string
     */
    protected static function file(): string
    {
        if (static::$file !== '') {
            return static::$file;
        }
        $base = function_exists('base_path')
            ? (string)base_path()
            : (defined('BASE_PATH') ? (string)BASE_PATH : (string)getcwd());
        return static::$file = rtrim(str_replace('\\', '/', $base), '/') . '/nginx.conf';
    }

    /**
     * 读取文件原文（按修改时间 + 大小缓存，避免常驻进程反复读盘）
     * @return string
     */
    protected static function read(): string
    {
        $file = static::file();
        clearstatcache(true, $file);
        $exists = is_file($file);
        $mtime = $exists ? (int)filemtime($file) : 0;
        $size = $exists ? (int)filesize($file) : 0;
        $cache = static::$cache;
        if ($cache && $cache['file'] === $file && $cache['mtime'] === $mtime && $cache['size'] === $size) {
            return $cache['content'];
        }
        $content = $exists ? (string)file_get_contents($file) : '';
        // 兼容带 BOM 的文件
        $content = ltrim($content, "\xEF\xBB\xBF");
        static::$cache = ['file' => $file, 'mtime' => $mtime, 'size' => $size, 'content' => $content];
        return $content;
    }

    /**
     * 写回文件并刷新缓存
     * @param string $content 待写入的完整内容
     * @return bool 成功返回 true，写盘失败返回 false
     */
    protected static function write(string $content): bool
    {
        $file = static::file();
        $dir = dirname($file);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        if (@file_put_contents($file, $content, LOCK_EX) === false) {
            return false;
        }
        clearstatcache(true, $file);
        static::$cache = [
            'file'    => $file,
            'mtime'   => (int)filemtime($file),
            'size'    => (int)filesize($file),
            'content' => $content,
        ];
        return true;
    }

    /**
     * 从原文中解析 proxy_pass 端口
     * @param string $content 文件原文
     * @return int|null 命中返回端口，未命中返回 null
     */
    protected static function port(string $content): ?int
    {
        if (preg_match('/proxy_pass\s+https?:\/\/[^:;\s]+:(\d+)/i', $content, $m)) {
            return (int)$m[1];
        }
        return null;
    }

    /**
     * 修改 proxy_pass 端口
     * @param string $content 文件原文
     * @param mixed  $value   新端口
     * @return string|null 返回新内容；无 proxy_pass 可定位时返回 null
     * @throws RuntimeException 端口非法
     */
    protected static function setPort(string $content, mixed $value): ?string
    {
        if ($value === '' || $value === null || !preg_match('/^\d+$/', (string)$value)) {
            throw new RuntimeException('nginx 端口必须是数字');
        }
        $port = (int)$value;
        if ($port < 1 || $port > 65535) {
            throw new RuntimeException('nginx 端口超出有效范围（1-65535）');
        }
        $count = 0;
        $new = preg_replace_callback(
            '/(proxy_pass\s+https?:\/\/[^:;\s]+:)(\d+)/i',
            function ($m) use ($port, &$count) {
                $count++;
                return $m[1] . $port;
            },
            $content
        );
        return $count > 0 ? $new : null;
    }

    /**
     * 按指令名读取单行指令值（如 proxy_buffering、proxy_http_version）
     * @param string $content 文件原文
     * @param string $name    指令名
     * @return string|null 命中返回指令值，未命中返回 null
     */
    protected static function directive(string $content, string $name): ?string
    {
        if (!static::isValidName($name)) {
            return null;
        }
        $pattern = '/^\s*' . preg_quote($name, '/') . '\s+([^;\r\n]+);/m';
        if (preg_match($pattern, $content, $m)) {
            return trim($m[1]);
        }
        return null;
    }

    /**
     * 按指令名修改单行指令值，指令不存在时在顶层（location 块之前）追加
     * @param string $content 文件原文
     * @param string $name    指令名
     * @param mixed  $value   指令值（会转为字符串）
     * @return string|null 返回新内容；指令名非法时返回 null
     */
    protected static function setDirective(string $content, string $name, mixed $value): ?string
    {
        if (!static::isValidName($name)) {
            return null;
        }
        $newValue = static::stringify($value);
        $count = 0;
        $pattern = '/^(\s*)' . preg_quote($name, '/') . '(\s+)([^;\r\n]+)(;)/m';
        $new = preg_replace_callback(
            $pattern,
            function ($m) use ($name, $newValue, &$count) {
                $count++;
                return $m[1] . $name . $m[2] . $newValue . $m[4];
            },
            $content
        );
        if ($count > 0) {
            return $new;
        }
        $eol = static::detectEol($content);
        $line = "{$name} {$newValue};";
        // 指令不存在：在顶层首个 location 块之前单独成行追加，保持与 proxy_buffering 同层级
        if (preg_match('/^(\s*)location\b/m', $content)) {
            return preg_replace('/^(\s*location\b)/m', $line . $eol . '$1', $content, 1);
        }
        return rtrim($content) . $eol . $line . $eol;
    }

    /**
     * 校验 nginx 指令名是否合法（仅字母、数字、下划线，且不以数字开头）
     * @param string $name
     * @return bool
     */
    protected static function isValidName(string $name): bool
    {
        return $name !== '' && preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name) === 1;
    }

    /**
     * 将配置值转为可写入单行指令的字符串（去掉分号与换行，避免破坏语法）
     * @param mixed $value
     * @return string
     */
    protected static function stringify(mixed $value): string
    {
        if (is_bool($value)) {
            $value = $value ? 'on' : 'off';
        } elseif ($value === null) {
            $value = '';
        } else {
            $value = (string)$value;
        }
        return trim(str_replace([';', "\r", "\n"], '', $value));
    }

    /**
     * 探测原文使用的主换行符
     * @param string $content
     * @return string
     */
    protected static function detectEol(string $content): string
    {
        return str_contains($content, "\r\n") ? "\r\n" : "\n";
    }
}
