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
 * @param array $option 配置选项
 * - slash: 是否拼接前斜杠(默认:是)
 * - domain: 是否生成完整域名(默认:否)
 * - module: 是否拼接模块名称(默认:是)
 * - escape: 是否转义query参数(默认:否)
 * @return string
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
function xbUrl(string $url, array $query = [], array $option = [])
{
    // 是否拼接前斜杠(默认:是)
    $slash = $option['slash'] ?? true;
    // 是否生成完整域名(默认:否)
    $domain = $option['domain'] ?? false;
    // 是否拼接模块名称(默认:是)
    $module = $option['module'] ?? true;
    // 是否转义query参数(默认:否)
    $escape = $option['escape'] ?? false;

    if ($module) {
        // 获取插件名称
        $plugin = request()->plugin;
        // 获取模块名称
        $moduleName = request()->app;
        // 拼接模块地址
        $url = "app/{$plugin}/{$moduleName}/{$url}";
    }else{
        // 无模块地址
        $url = $domain ? $url : "app/{$url}";
    }
    // 是否拼接前斜杠
    if ($slash) {
        $url = '/' . $url;
    }
    // 是否生成完整域名
    if ($domain) {
        $domainUrl = request()->host();
        $domainUrl = "http://{$domainUrl}";
        // 如果slash已经拼接了，后面就不要在拼接了
        $domainUrl = $slash ? $domainUrl : "{$domainUrl}/";
        $url = $domainUrl . $url;
    }
    // 拼接地址栏参数
    if ($query) {
        // 如果query参数是字符串，则直接使用(默认是转义)
        $query = http_build_query($query);
        $url .= '?' . ($escape ? $query : urldecode($query));
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