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
    if (!file_exists($path))
    {
        return $default;
    }
    static $config = [];
    if (!$config)
    {
        // 解析文件
        $config = @parse_ini_file($path, true) ?? [];
    }
    // 返回所有配置
    if (!$name)
    {
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
    if ($scene)
    {
        $class->scene($scene);
    }
    $result = $class->check($data);
    if (!$result)
    {
        throw new Exception((string)$class->getError(), 404);
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
    // 设置总后台路由格式
    $path       = $request->path();
    $data       = array_values(array_filter(explode('/', $path)));
    $moduleName = '';
    if (count($data) >= 3)
    {
        $moduleName = $data[0] ?? '';
    }
    return $moduleName;
}

/**
 * 生成URL地址
 * @param string $url 生成地址
 * @param array $params 地址参数
 * @param bool $slash 是否带前缀斜杠
 * @return string
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
function xbUrl(string $url = '', array $params = [], bool $slash = true)
{
    $moduleName = xbModuleName();
    $path       = "{$moduleName}/{$url}";
    if ($slash)
    {
        $path = "/{$path}";
    }
    if ($params) {
        // 拼接参数
        $path .= '?' . http_build_query($params);
    }
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
    if (is_array($list))
    {
        $refer = $resultSet = array();
        foreach ($list as $i => $data)
            $refer[$i] = &$data[$field];
        switch ($sortby)
        {
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
function p(mixed $data)
{
    if (config('app.debug')) {
        echo PHP_EOL;
        print_r($data);
        echo PHP_EOL;
    }
}