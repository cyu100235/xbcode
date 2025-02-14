<?php
/**
 * 打印数据
 * @param mixed $data
 * @param string $remarks
 * @return void
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
function p(mixed $data, string $remarks = '')
{
    $debug = getenv('APP_DEBUG') === 'true';
    if ($debug) {
        if (empty($remarks)) {
            $remarks = '打印数据';
        }
        echo '--------';
        echo $remarks;
        echo '--------';
        echo PHP_EOL;
        print_r($data);
        echo PHP_EOL;
    }
}
/**
 * 解析nginx配置文件
 * @param string $file
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
function xbGetNginxConf(string $file)
{
    // 读取文件内容
    $content = file_get_contents($file);
    if (empty($content)) {
        return null;
    }
    // 解析nginx配置文件
    preg_match('/proxy_pass\s+http:\/\/\d+\.\d+\.\d+\.\d+:(\d+)/', $content, $matches);
    // 获取端口号
    $port = $matches[1] ?? null;
    return $port;
}
if (!function_exists('xbServerPort')) {
    /**
     * 获取端口号
     * @return int|null
     */
    function xbServerPort(int $default = 39000)
    {
        // 获取宝塔环境配置
        $btEnv = getenv('BT_ENV_STATE') === 'true';
        $name = getenv('BT_ENV_NAME');
        $file = "/www/server/panel/vhost/rewrite/{$name}.conf";
        // 检测是否宝塔环境
        if ($btEnv && $name && file_exists($file)) {
            // 获取nginx配置文件端口号
            $port = xbGetNginxConf($file);
            if ($port) {
                return (int) $port;
            }
        }
        // 检测本地伪静态文件
        $file = base_path() . '/nginx.conf';
        if (file_exists($file)) {
            // 获取nginx配置文件端口号
            $port = xbGetNginxConf($file);
            if ($port) {
                return (int) $port;
            }
        }
        // 检测插件伪静态文件
        $file = base_path() . '/plugin/xbCode/nginx.conf';
        if (file_exists($file)) {
            // 获取nginx配置文件端口号
            $port = xbGetNginxConf($file);
            if ($port) {
                return (int) $port;
            }
        }
        // 返回默认端口号
        return $default;
    }
}

/**
 * 验证数据
 * @param mixed $validate
 * @param array $data
 * @param string $scene
 * @return void
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
function xbValidate($validate, array $data, string $scene = '')
{
    // 实例类
    $class = new $validate;
    // 场景验证
    if ($scene) {
        $class->scene($scene);
    }
    $result = $class->check($data);
    if (!$result) {
        throw new \Exception((string) $class->getError(), 404);
    }
}
/**
 * 生成URL地址
 * @param string $url 路由地址
 * @param array $query 附带参数
 * @param bool $slash 是否拼接前斜杠
 * @param bool $domain 是否生成完整域名
 * @param bool $module 是否拼接模块名称
 * @return string
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
function xbUrl(string $url, array $query = [], bool $slash = false, bool $domain = false, bool $module = true)
{
    $plugin = request()->plugin;
    if ($plugin) {
        $moduleName = request()->app;
        $moduleName = $module ? "{$moduleName}/" : '';
        // 拼接插件地址
        $url = "app/{$plugin}/{$moduleName}{$url}";
    } else {
        $moduleName = xbAppName();
        $moduleName = $module ? "{$moduleName}/" : '';
        // 拼接应用地址
        $url = "{$moduleName}{$url}";
    }
    // 是否拼接前斜杠
    if ($slash) {
        $url = '/' . $url;
    }
    // 是否生成完整域名
    if ($domain) {
        $domainUrl = request()->host();
        $domainUrl = "http://{$domainUrl}";
        $domainUrl = $slash ? $domainUrl : "{$domainUrl}/";
        $url = $domainUrl . $url;
    }
    // 拼接地址栏参数
    if ($query) {
        $url .= '?' . http_build_query($query);
    }
    // 返回地址
    return $url;
}
/**
 * 获取应用名称
 * @return string|null
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
function xbAppName()
{
    $name = request()->app;
    if (request()->plugin) {
        $name = request()->plugin;
    }
    return $name;
}
/**
 * 获取文件大小
 * @param int $size
 * @param int $decimals
 * @return string
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
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
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
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
 * 获取请求信息
 * @param mixed $path
 * @return array
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
function xbPathInfo($path)
{
    $path = ltrim(trim($path, '/'), '/');
    $suffix = config('app.controller_suffix', '');
    $plugin = request()->plugin;
    $module = request()->app;
    $paths = str_replace("app/{$plugin}/", '', $path);
    $paths = explode('/', $paths);
    if (count($paths) <= 1) {
        return [];
    }
    $controller = $paths[1] ?? '';
    $controller = ucfirst($controller);
    $action = $paths[2] ?? '';
    $class = "\\plugin\\{$plugin}\\app\\{$module}\\controller\\{$controller}{$suffix}";
    if (!class_exists($class)) {
        throw new Exception("class not exists: {$class}");
    }
    return [
        'module' => $module,
        'controller' => $controller,
        'action' => $action,
        'fullPath' => $path,
        'path' => "{$module}/{$controller}/{$action}",
        'uri' => $path,
        'class' => $class,
    ];
}