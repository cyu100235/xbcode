<?php

/**
 * 获取环境变量
 * @param string $name
 * @param mixed $default
 * @return mixed
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
function xbEnv(string $name = '', $default = null)
{
    $path = base_path('.env');
    static $config = [];
    if (!$config) {
        // 解析文件
        $config = @parse_ini_file($path, true) ?? [];
    }
    // 返回所有配置
    if (!$name) {
        return $config;
    }
    @[$one, $two] = explode('.', $name);
    @[$one => $item] = $config;
    return $two === null ? $item ?? $default : $item[$two] ?? $default;
}
/**
 * 验证器
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
        throw new Exception((string) $class->getError(), 404);
    }
}

/**
 * 获取模块名称
 * @throws \Exception
 * @return mixed
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
function xbModuleName()
{
    $request = request();
    // 插件模块
    if ($request->plugin) {
        return "app/{$request->plugin}";
    }
    // 总后台模块
    $path = $request->path();
    $data = array_values(array_filter(explode('/', $path)));
    $moduleName = '';
    if (count($data) >= 3) {
        $moduleName = $data[0] ?? '';
    }
    return $moduleName;
}

/**
 * 获取域名网址
 * @param bool $full
 * @return string
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
function xbDomain(bool $full = true)
{
    $request = request();
    $url = (string) $request->header('host', '');
    if (strrpos($url, '127.0.0.1') !== false) {
        $url = '';
    }
    if (empty($url)) {
        $url = (string) $request->header('x-host', '');
        $url = parse_url($url, PHP_URL_HOST);
    }
    if (strrpos($url, '127.0.0.1') !== false) {
        $url = '';
    }
    if (empty($url)) {
        $url = (string) $request->header('x-real-host', '');
        $url = parse_url($url, PHP_URL_HOST);
    }
    if (strrpos($url, '127.0.0.1') !== false) {
        $url = '';
    }
    if (empty($url)) {
        return '';
    }
    if ($full) {
        $proto = $request->header('x-forwarded-proto', 'http');
        $scheme = $request->header('x-scheme', 'http');
        if ($scheme == 'https' || $proto == 'https') {
            $proto = "https";
        }
        $url = "{$proto}://{$url}";
    }
    return $url;
}

/**
 * 生成URL地址
 * @param string $url 地址
 * @param array $params 参数
 * @param bool $slash 是否带斜杠
 * @param bool $full 是否完整URL
 * @return string
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
function xbUrl(string $url = '', array $params = [], bool $slash = true, bool $full = false)
{
    $moduleName = xbModuleName();
    $path = "{$moduleName}/{$url}";
    if ($slash) {
        $path = "/{$path}";
    }
    if ($params) {
        // 拼接参数
        $path .= '?' . http_build_query($params);
    }
    // 是否完整URL
    if ($full) {
        $domain = xbDomain();
        $path = $domain . $path;
    }
    // 返回地址
    return $path;
}

/**
 * 对查询结果集进行排序
 * asc正向排序 desc逆向排序 nat自然排序
 * @param array $list
 * @param string $field
 * @param mixed $sortby
 * @return array|bool
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
function list_sort_by(array $list, string $field, $sortby = 'asc')
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
 * 打印函数
 * @param mixed $data
 * @return void
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
function p(mixed $data, string $remarks = '')
{
    if (config('app.debug')) {
        if ($remarks) {
            echo '--------';
            echo $remarks;
            echo '--------';
        }
        echo PHP_EOL;
        print_r($data);
        echo PHP_EOL;
    }
}

/**
 * 渲染视图模板文件
 * @param string $file
 * @param array $data
 * @param string $suffix
 * @return bool|string
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
function xbView(string $file, array $data = [], string $suffix = 'vue')
{
    $path = base_path("{$file}.{$suffix}");
    if (!file_exists($path)) {
        throw new Exception("视图文件不存在: {$file}", 404);
    }
    $content = file_get_contents($path);
    if (!$content) {
        throw new Exception("视图文件内容为空: {$file}", 404);
    }
    return $content;
}
