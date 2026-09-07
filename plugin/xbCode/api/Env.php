<?php
/**
 * xbCode 自定义 Env 读取器（对外仅开放 get() / set()）
 *
 * 核心约定：
 *   - Env::get() 读取值时，会**在返回前自动解析字面量类型**，按 dotenv 常见字面量得到 PHP 原生类型：
 *       "true"/"yes"/"on"  -> true
 *       "false"/"no"/"off" -> false
 *       "null"/"empty"     -> null
 *       整数字面量         -> int
 *       小数字面量/科学计数 -> float
 *       其它               -> string
 *   - 即使调用方传入的 $default 是字符串（如 Env::get('X', '6379')），返回前也会做一次同样的类型解析，
 *     保证 REDIS_PORT / SESSION_LIFETIME 这类数字型配置不会因为没在 .env 写就退回成字符串。
 *   - 动态设置：set(string|array, mixed) 支持运行期单键 / 批量覆盖（仅内存，不写磁盘）。
 *
 * 对外 API：
 *   - Env::get(string $key, mixed $default = null): mixed
 *   - Env::set(string|array<string, mixed> $key, mixed $value = null): void
 *
 * @package xbCode
 * @license Apache License 2.0
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */

namespace plugin\xbCode\api;

class Env
{
    /** @var array<string, string> .env 原文/运行期字符串值（保留原始字面量，供 get() 每次按统一规则解析） */
    protected static array $raw = [];

    /** @var array<string, mixed> 运行期 set 写入的非字符串值（null/int/bool/float/array/object 直接走原生存储，不走 cast） */
    protected static array $native = [];

    /** @var bool 是否已执行过加载 */
    protected static bool $loaded = false;

    /** @var string|null 最近一次加载的 .env 绝对路径 */
    protected static ?string $loadedFile = null;

    /**
     * 读取单个值（返回前自动解析数据类型）
     *
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        if (!self::$loaded) {
            self::load();
        }
        $key = trim($key);
        if ($key === '') {
            return self::castMixed($default);
        }
        $up = strtoupper($key);
        $hit = null;
        $has = false;
        // 1) 原生存储优先级最高（运行期 set() 进来的非字符串 / 任意类型，保持原样）
        if (array_key_exists($up, self::$native)) {
            return self::castMixed(self::$native[$up]);
        }
        // 2) 字符串存储（.env 文件加载进来的原文 / set 进来的字符串），走统一 cast 字面量解析
        if (array_key_exists($up, self::$raw)) {
            return self::cast(self::$raw[$up]);
        }
        // 3) 大小写兼容（允许传小写 key）
        if ($up !== $key) {
            if (array_key_exists($key, self::$native)) {
                return self::castMixed(self::$native[$key]);
            }
            if (array_key_exists($key, self::$raw)) {
                return self::cast(self::$raw[$key]);
            }
        }
        // 4) 命中不到 -> 返回 $default（同样要按 default 类型做解析）
        return self::castMixed($default);
    }

    /**
     * 动态设置（仅内存，不写磁盘）
     *   - Env::set('KEY', '6379')   字符串按原始字面量存储，get() 读取时才 cast 为 int 6379
     *   - Env::set('KEY', 6379)     原生类型直接保留，get() 再按原生类型 castMixed 返回
     *   - Env::set(['K1'=>'v','K2'=>2]) 批量覆盖 / 追加
     *
     * @param string|array<string, mixed> $key
     * @param mixed                       $value
     * @return void
     */
    public static function set(string|array $key, mixed $value = null): void
    {
        if (!self::$loaded) {
            self::load();
        }
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                self::doSet($k, $v);
            }
            return;
        }
        self::doSet($key, $value);
    }

    /* ---------------------------- 内部能力（不对外暴露） ---------------------------- */

    /**
     * 加载 .env 文件（默认 BASE_PATH/.env）
     * @param string|null $file   绝对路径；留空自动推断
     * @param bool        $merge  true=合并,false=重置后重加载
     */
    protected static function load(?string $file = null, bool $merge = true): void
    {
        if ($file === null) {
            $base = defined('BASE_PATH') ? (string)BASE_PATH : getcwd();
            $file = rtrim(str_replace('\\', '/', $base), '/') . '/.env';
        }
        self::$loadedFile = $file;
        if (!$merge) {
            self::$raw    = [];
            self::$native = [];
        }
        if (is_file($file)) {
            $lines = @file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if ($lines !== false) {
                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line === '' || $line === '0' || $line[0] === '#' || str_starts_with($line, ';')) {
                        // 注意：以 '0' 开头这行其实只可能是 "0=..." 这种非法 key 或空注释，安全跳过首字符即可
                        if ($line !== '' && $line !== '0' && $line[0] !== '#' && !str_starts_with($line, ';')) {
                            // 非注释：回退继续解析（避免把 "FOO=0" 这种首字符 0 的值跳了）
                        }
                    }
                    if ($line === '' || $line[0] === '#' || str_starts_with($line, ';')) {
                        continue;
                    }
                    $pos = strpos($line, '=');
                    if ($pos === false) {
                        continue;
                    }
                    $key   = trim(substr($line, 0, $pos));
                    $value = trim(substr($line, $pos + 1));
                    if ($key === '' || !preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $key)) {
                        continue;
                    }
                    $value = self::stripInlineComment($value);
                    $value = self::unquote($value);
                    self::doSetRaw($key, $value);
                }
            }
        }
        self::$loaded = true;
    }

    /** 强制重载（按磁盘 .env 重建数据，丢掉内存动态覆盖） */
    protected static function reload(?string $file = null): void
    {
        self::load($file, false);
    }

    /**
     * 返回整份快照（key => cast 后原生类型值）
     * @return array<string, mixed>
     */
    protected static function all(): array
    {
        if (!self::$loaded) {
            self::load();
        }
        $out = [];
        $keys = array_unique(array_merge(array_keys(self::$raw), array_keys(self::$native)));
        foreach ($keys as $k) {
            $out[$k] = self::get($k);
        }
        return $out;
    }

    /** 判断是否包含指定 key（大小写不敏感） */
    protected static function has(string $key): bool
    {
        if (!self::$loaded) {
            self::load();
        }
        $k1 = $key;
        $k2 = strtoupper($key);
        return
            array_key_exists($k1, self::$raw)    || array_key_exists($k2, self::$raw)    ||
            array_key_exists($k1, self::$native) || array_key_exists($k2, self::$native);
    }

    /** put = set 的别名，保留为受保护方法用于内部语义切换 */
    protected static function put(string|array $key, mixed $value = null): void
    {
        self::set($key, $value);
    }

    /**
     * 清空指定键 / 全部数据
     * @param string|array<string>|null $key 留空 = 清空全部
     */
    protected static function clear(string|array|null $key = null): void
    {
        if (!self::$loaded) {
            self::load();
        }
        if ($key === null) {
            self::$raw    = [];
            self::$native = [];
            return;
        }
        foreach ((array)$key as $k) {
            $kUp = strtoupper($k);
            unset(self::$raw[$k], self::$raw[$kUp]);
            unset(self::$native[$k], self::$native[$kUp]);
        }
    }

    /**
     * 按前缀分组读取：所有 key 以 "{$prefix}{$delimiter}" 开头的项去掉前缀后聚合返回，
     * 返回值中的每个子项都会走 get()，自然获得"读取时自动解析"后的原生类型。
     *
     *   Env::group('DB')        -> [HOST, PORT, NAME, USER, PASS, CHARSET...]
     *   Env::group('DB', true)  -> [host, port, name, user, pass, charset...] (子 key 小写)
     * @return array<string, mixed>
     */
    protected static function group(string $prefix, bool $lowerKey = false, string $delimiter = '_'): array
    {
        if (!self::$loaded) {
            self::load();
        }
        $prefix = rtrim(strtoupper($prefix), $delimiter) . $delimiter;
        $len    = strlen($prefix);
        $result = [];
        $keys   = array_unique(array_merge(array_keys(self::$raw), array_keys(self::$native)));
        foreach ($keys as $key) {
            if (str_starts_with(strtoupper($key), $prefix)) {
                $subKey = substr($key, $len);
                $result[$lowerKey ? strtolower($subKey) : $subKey] = self::get($key);
            }
        }
        return $result;
    }

    /** 返回最后一次加载过的 .env 文件路径 */
    protected static function getLoadedFile(): ?string
    {
        return self::$loadedFile;
    }

    /* ---------------------------- helpers ---------------------------- */

    /**
     * 只在存储值是字符串时才进行 dotenv 字面量解析；其它类型按 PHP 原生语义 castMixed() 轻量规整。
     * cast() 本身是 dotenv 字符串 -> PHP 原生类型 的单向映射。
     */
    private static function cast(string $raw): mixed
    {
        if ($raw === '') {
            return '';
        }
        $lower = strtolower($raw);
        if (in_array($lower, ['true', '(true)', 'yes', 'on'], true)) {
            return true;
        }
        if (in_array($lower, ['false', '(false)', 'no', 'off'], true)) {
            return false;
        }
        if (in_array($lower, ['null', '(null)', 'empty', '(empty)'], true)) {
            return null;
        }
        if (preg_match('/\A-?(?:0|[1-9]\d*)\z/', $raw) === 1) {
            $intVal = (int)$raw;
            if ((string)$intVal === ltrim($raw, '-') || ($raw[0] === '-' && substr($raw, 1) === (string)$intVal)) {
                return $intVal;
            }
            if (function_exists('filter_var')) {
                $int = filter_var($raw, FILTER_VALIDATE_INT);
                if ($int !== false) {
                    return $int;
                }
            }
        }
        if (is_numeric($raw)) {
            $float = (float)$raw;
            if (ctype_digit(ltrim($raw, '-')) && abs((int)$float - $float) < PHP_FLOAT_EPSILON) {
                return (int)$float;
            }
            return $float;
        }
        return $raw;
    }

    /**
     * 对任意 $mixed 默认值/运行期 set 值做"最后一次返回前规整"：
     *  - string  => 走 cast()（保证默认值 '6379' -> int 6379；'true' -> true 等）
     *  - int/float/bool/array/object/resource  => 原样返回
     *  - null  => null
     */
    private static function castMixed(mixed $value): mixed
    {
        if (is_string($value)) {
            return self::cast($value);
        }
        return $value;
    }

    /** 去掉首尾一对匹配的单引号或双引号 */
    private static function unquote(string $value): string
    {
        $len = strlen($value);
        if ($len >= 2) {
            $first = $value[0];
            $last  = $value[$len - 1];
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                return substr($value, 1, -1);
            }
        }
        return $value;
    }

    /** 去除行尾注释 #，引号包裹的内部不拆分 */
    private static function stripInlineComment(string $value): string
    {
        $len    = strlen($value);
        $quote  = null;
        $escape = false;
        for ($i = 0; $i < $len; $i++) {
            $ch = $value[$i];
            if ($escape) {
                $escape = false;
                continue;
            }
            if ($quote !== null) {
                if ($ch === '\\') {
                    $escape = true;
                    continue;
                }
                if ($ch === $quote) {
                    $quote = null;
                    continue;
                }
                continue;
            }
            if ($ch === '"' || $ch === "'") {
                $quote = $ch;
                continue;
            }
            if ($ch === '#') {
                return rtrim(substr($value, 0, $i));
            }
        }
        return $value;
    }

    /** 运行期 set 的单键处理：字符串统一扔 $raw（保留原文 cast），非字符串走 $native 原生类型保留。 */
    private static function doSet(string $key, mixed $value): void
    {
        if ($key === '') {
            return;
        }
        $k = strtoupper($key);
        // 统一先清掉同 key 的另一路，保证 $raw / $native 不会同时出现
        unset(self::$native[$k], self::$raw[$k]);
        if (is_string($value)) {
            self::$raw[$k] = $value;
            $_ENV[$k]      = self::cast($value);
            $_SERVER[$k]   = $value;
            return;
        }
        self::$native[$k] = $value;
        $_ENV[$k]         = self::castMixed($value);
        $_SERVER[$k]      = self::toString(self::castMixed($value));
    }

    /** .env 加载落盘：永远进 $raw。 */
    private static function doSetRaw(string $key, string $value): void
    {
        if ($key === '') {
            return;
        }
        $k = strtoupper($key);
        unset(self::$native[$k]);
        self::$raw[$k] = $value;
        if (!isset($_ENV[$k])) {
            $_ENV[$k] = self::cast($value);
        }
        $_SERVER[$k] = $value;
    }

    private static function toString(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if ($value === null) {
            return '';
        }
        if (is_scalar($value)) {
            return (string)$value;
        }
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}