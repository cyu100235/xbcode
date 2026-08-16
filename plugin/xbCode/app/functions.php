<?php

/**
 * 打印数据
 * @param mixed $data
 * @param string $remarks
 * @return void
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
function p(mixed $data, string $remarks = '')
{
    if (empty($remarks)) {
        $remarks = '打印数据';
    }
    $output = '--------' . $remarks . '--------' . PHP_EOL;
    $output .= print_r($data, true);
    $output .= PHP_EOL;
    // 优先使用 Workerman safeEcho（Workerman 环境下避免重复输出）
    // 仅当 safeEcho 不可用时，回退到 STDOUT（Windows 多层 proc_open 兜底）
    if (class_exists(\Workerman\Worker::class) && method_exists(\Workerman\Worker::class, 'safeEcho')) {
        \Workerman\Worker::safeEcho($output);
    } elseif (defined('STDOUT') && is_resource(STDOUT)) {
        fwrite(STDOUT, $output);
        fflush(STDOUT);
    }
    // 3. 写入日志文件作为兜底（Windows 下控制台可能不可达）
    if (class_exists(\support\Log::class)) {
        \support\Log::channel('default')->debug($remarks, is_array($data) ? $data : ['data' => $data]);
    }
}

/**
 * 验证数据
 * @param string $validate 验证器类
 * @param array $data 验证数据
 * @param string|array  $scene 验证场景或验证字段
 * @throws Exception
 * @return void
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
function xbValidate(string $validate, array $data, string|array $scene = '')
{
    /** @var \taoser\Validate */
    $class = new $validate;
    // 场景验证
    if ($scene && is_string($scene)) {
        $class->scene($scene);
    }
    // 验证字段
    if ($scene && is_array($scene)) {
        $class->only($scene);
    }
    $result = $class->check($data);
    if (!$result) {
        throw new \Exception((string) $class->getError(), 404);
    }
}
/**
 * 获取文件大小
 * @param int $size
 * @param int $decimals
 * @return string
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
function get_size(int $size, int $decimals = 2): string
{
    switch (true) {
        case $size >= pow(1024, 3):
            return round($size / pow(1024, 3), $decimals) . " GB";
        case $size >= pow(1024, 2):
            return round($size / pow(1024, 2), $decimals) . " MB";
        case $size >= pow(1024, 1):
            return round($size / pow(1024, 1), $decimals) . " KB";
        default:
            return $size . 'B';
    }
}
/**
 * 对查询结果集进行排序
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param string $sortby 排序类型
 * @return array|bool
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
function list_sort_by(array $list, string $field, string $sortby = 'asc')
{
    if (is_array($list)) {
        $refer = $resultSet = array();
        foreach ($list as $i => $data)
            $refer[$i] = &$data[$field];
        switch ($sortby) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc': // 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key => $val)
            $resultSet[] = &$list[$key];
        return $resultSet;
    }
    return false;
}

/**
 * 获取路由地址数据
 * @param string $class 控制器类
 * @param string $action 方法名
 * @return array
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
function xbPathInfo(string $class, string $action)
{
    // 插件标识
    $plugin = request()->plugin;
    // 模块标识
    $module = request()->app;
    // 控制器后缀
    $suffix = config('app.controller_suffix', '');
    // 控制器名称
    $controller = class_basename($class);
    $controller = str_replace($suffix, '', $controller);
    // 路径地址
    $path = "{$controller}/{$action}";
    // 导出数据
    return [
        'plugin' => $plugin,
        'module' => $module,
        'controller' => $controller,
        'action' => $action,
        'fullPath' => $path,
        'path' => "{$module}/{$controller}/{$action}",
        'uri' => $path,
        'class' => $class,
    ];

}
if (!function_exists('array_find')) {
    /**
     * 查找数组中符合条件的第一个元素
     * @param array $array 要查找的数组
     * @param callable $callback 回调函数，用于判断元素是否符合条件
     * @return mixed 返回符合条件的第一个元素，若不存在则返回null
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    function array_find(array $array, callable $callback): mixed
    {
        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }
        return null;
    }
}
if (!function_exists('toUnderScore')) {
    /**
     * 驼峰命名转下划线命名
     * @param string $str 字符串
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    function toUnderScore(string $str)
    {
        $dstr = preg_replace_callback('/([A-Z]+)/', function ($matchs) {
            return '_' . strtolower($matchs[0]);
        }, $str);
        return trim(preg_replace('/_{2,}/', '_', $dstr), '_');
    }
}
if (!function_exists('time_ago')) {
    /**
     * 将时间戳或日期时间字符串转换为友好的相对时间描述
     * @param int|string $time 时间戳或日期时间字符串（如 2024-01-01 12:00:00）
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    function time_ago(int|string $time): string
    {
        // 统一转为时间戳
        $timestamp = is_numeric($time) ? (int) $time : strtotime((string) $time);
        if ($timestamp === false || $timestamp === 0) {
            return '未知时间';
        }

        $now  = time();
        $diff = $now - $timestamp;

        // 未来时间兜底
        if ($diff < 0) {
            return date('Y-m-d H:i', $timestamp);
        }

        if ($diff < 60) {
            return '刚刚';
        }

        if ($diff < 3600) {
            return (int) ($diff / 60) . '分钟前';
        }

        if ($diff < 86400) {
            return (int) ($diff / 3600) . '小时前';
        }

        // 判断昨天 / 前天（按自然日）
        $todayStart     = mktime(0, 0, 0);
        $yesterdayStart = $todayStart - 86400;
        $dayBeforeStart = $todayStart - 172800;

        if ($timestamp >= $yesterdayStart && $timestamp < $todayStart) {
            return '昨天';
        }

        if ($timestamp >= $dayBeforeStart && $timestamp < $yesterdayStart) {
            return '前天';
        }

        if ($diff < 2592000) {
            return (int) ($diff / 86400) . '天前';
        }

        if ($diff < 31536000) {
            return (int) ($diff / 2592000) . '个月前';
        }

        return (int) ($diff / 31536000) . '年前';
    }
}
if (!function_exists('toCamelCase')) {
    /**
     * 下划线命名到驼峰命名
     * @param string $str 字符串
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    function toCamelCase(string $str)
    {
        $array = explode('_', $str);
        $result = $array[0];
        $len = count($array);
        if ($len > 1) {
            for ($i = 1; $i < $len; $i++) {
                $result .= ucfirst($array[$i]);
            }
        }
        return $result;
    }
}
if (!function_exists('is_valid_url')) {
    /**
     * 验证URL是否合法有效
     * @param string $url 待验证的URL地址
     * @param bool $checkDns 是否检查DNS解析（验证域名是否真实存在），默认false
     * @return bool 合法返回true，否则返回false
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    function is_valid_url(string $url, bool $checkDns = false): bool
    {
        // 基础格式校验：必须是 http 或 https 协议
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        // 只允许 http / https 协议
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        if (!in_array($scheme, ['http', 'https'], true)) {
            return false;
        }
        // 主机不能为空
        $host = parse_url($url, PHP_URL_HOST);
        if (empty($host)) {
            return false;
        }
        // 可选：DNS 解析验证（检查域名是否真实存在）
        if ($checkDns && !checkdnsrr((string) $host, 'ANY')) {
            return false;
        }
        return true;
    }
}