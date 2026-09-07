<?php
/**
 * 微服务互通令牌读写（对外仅开放 get() / set()）
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */

namespace plugin\xbCode\api;

use RuntimeException;

/**
 * ServerToken 互通令牌读写器
 *
 * 对外只暴露 get / set 两个方法，固定读写主项目 runtime/server_token（单行令牌文本），用法：
 *
 * ServerToken::get();                // 读取当前令牌，文件不存在时自动生成随机令牌并落盘
 * ServerToken::set();                // 重新生成随机令牌并落盘，返回新令牌
 * ServerToken::set('your-token');    // 写入指定令牌，返回该令牌
 *
 * 说明：令牌固定为 64 位十六进制随机串；自动生成走独占创建（fopen 'x'），
 *      多进程并发初始化时也只有一份令牌落盘，未抢到的进程回读先写者的值；
 *      读取结果按文件修改时间与大小缓存，常驻内存进程下不会每次读盘。
 */
class ServerToken
{
    /**
     * 令牌长度（十六进制字符数）
     */
    protected const TOKEN_LENGTH = 64;

    /**
     * 令牌文件路径（惰性解析后缓存）
     * @var string
     */
    protected static string $file = '';

    /**
     * 内容缓存
     * @var array{file:string, mtime:int, size:int, token:string}|null
     */
    protected static ?array $cache = null;

    /**
     * 读取互通令牌
     * @return string 令牌文件里的内容；文件缺失或内容为空时自动生成随机令牌并落盘后返回
     * @throws RuntimeException 令牌目录创建失败、写盘失败
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function get(): string
    {
        $token = static::read();
        if ($token !== '') {
            return $token;
        }
        // 令牌文件缺失：独占创建，多进程并发初始化时只落一份；
        // 文件已存在但内容为空（上一次写入被中断）：独占创建已无意义，直接覆盖补齐
        return is_file(static::file())
            ? static::store(static::random())
            : static::store(static::random(), true);
    }

    /**
     * 写入互通令牌
     * @param string|null $token 令牌内容；留空表示重新生成随机令牌
     * @return string 实际落盘的令牌
     * @throws RuntimeException 令牌含空白等非法字符、令牌目录创建失败、写盘失败
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function set(?string $token = null): string
    {
        $token = trim((string) $token);
        if ($token === '') {
            return static::store(static::random());
        }
        if (preg_match('/^[\x21-\x7E]+$/', $token) !== 1) {
            throw new RuntimeException('互通令牌只能是除空白以外的可见字符');
        }
        return static::store($token);
    }

    /* ---------------------------- 内部能力（不对外暴露） ---------------------------- */

    /**
     * 令牌文件路径（默认主项目 runtime/server_token）
     * @return string
     */
    protected static function file(): string
    {
        if (static::$file !== '') {
            return static::$file;
        }
        try {
            // 优先跟随框架约定，兼容 config/app.php 自定义 runtime_path 的场景
            return static::$file = str_replace('\\', '/', runtime_path('server_token'));
        } catch (\Throwable $e) {
            // 兜底：脱离 webman 引导（如独立脚本）时按项目根 runtime 目录拼接
        }
        $base = defined('BASE_PATH') ? (string) BASE_PATH : (string) getcwd();
        return static::$file = rtrim(str_replace('\\', '/', $base), '/') . '/runtime/server_token';
    }

    /**
     * 落盘令牌并刷新缓存
     * @param string $token      令牌内容
     * @param bool   $createOnly true=仅在文件不存在时写入（并发初始化场景），false=覆盖写入
     * @return string 最终生效的令牌
     * @throws RuntimeException 令牌目录创建失败、写盘失败
     */
    protected static function store(string $token, bool $createOnly = false): string
    {
        $file = static::file();
        $dir  = dirname($file);
        if (!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new RuntimeException("互通令牌目录创建失败：{$dir}");
        }
        $content = $token . PHP_EOL;
        if ($createOnly) {
            $handle = @fopen($file, 'x');
            if ($handle === false) {
                // 其他进程已抢先落盘：不覆盖，清掉缓存回读先写者的令牌
                static::$cache = null;
                return static::read();
            }
            if (@fwrite($handle, $content) === false) {
                fclose($handle);
                // 只清理本次刚创建的空文件，避免留下读不出令牌的残留文件
                @unlink($file);
                throw new RuntimeException("互通令牌写入失败：{$file}");
            }
            fclose($handle);
        } elseif (@file_put_contents($file, $content, LOCK_EX) === false) {
            throw new RuntimeException("互通令牌写入失败：{$file}");
        }
        clearstatcache(true, $file);
        static::$cache = [
            'file'  => $file,
            'mtime' => (int) filemtime($file),
            'size'  => (int) filesize($file),
            'token' => $token,
        ];
        return $token;
    }

    /**
     * 读取令牌（按修改时间 + 大小缓存，避免常驻进程反复读盘）
     * @return string 文件不存在或内容为空时返回空字符串
     */
    protected static function read(): string
    {
        $file    = static::file();
        $exists  = is_file($file);
        $mtime   = $exists ? (int) filemtime($file) : 0;
        $size    = $exists ? (int) filesize($file) : 0;
        $cache   = static::$cache;
        if ($cache && $cache['file'] === $file && $cache['mtime'] === $mtime && $cache['size'] === $size) {
            return $cache['token'];
        }
        $content = $exists ? (string) file_get_contents($file) : '';
        // 兼容带 BOM 的文件
        $content = ltrim($content, "\xEF\xBB\xBF");
        $token   = trim($content, " \t\n\r\0\x0B\x1A");
        static::$cache = ['file' => $file, 'mtime' => $mtime, 'size' => $size, 'token' => $token];
        return $token;
    }

    /**
     * 生成随机令牌
     * @return string 64 位十六进制字符串
     */
    protected static function random(): string
    {
        $bytes = (int) ceil(self::TOKEN_LENGTH / 2);
        return substr(bin2hex(random_bytes($bytes)), 0, self::TOKEN_LENGTH);
    }
}
