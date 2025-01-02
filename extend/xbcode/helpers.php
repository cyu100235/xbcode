<?php
if (!function_exists('p')) {
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
        if (config('app.debug')) {
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
}
if (!function_exists('xbEnv')) {
    /**
     * 解析配置文件
     * @param string $name 配置名称
     * @param mixed $default 默认值
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    function xbEnv(string $name = '', $default = null)
    {
        $path = base_path() . '/.env';
        // 解析文件
        $config = @parse_ini_file($path, true) ?? [];
        // 返回所有配置
        if (!$name) {
            return $config;
        }
        @[$one, $two] = explode('.', $name);
        @[$one => $item] = $config;
        return $two === null ? $item ?? $default : $item[$two] ?? $default;
    }
}

if (!function_exists('xbValidate')) {
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
}
if (!function_exists('xbUrl')) {
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
        if ($module) {
            $name = xbAppName();
            if ($name && request()->plugin) {
                // 拼接插件地址
                $url = "app/{$name}/{$url}";
            } else {
                // 拼接应用地址
                $url = "{$name}/{$url}";
            }
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
            $url       = $domainUrl . $url;
        }
        // 拼接地址栏参数
        if ($query) {
            $url .= '?' . http_build_query($query);
        }
        // 返回地址
        return $url;
    }
}
if (!function_exists('xbAppName')) {
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
}
if (!function_exists('get_size')) {
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
}
if (!function_exists('list_sort_by')) {
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
}
if (!function_exists('xbPathInfo')) {
    /**
     * 获取请求信息
     * @param mixed $path
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    function xbPathInfo($path)
    {
        $path = trim($path, '/');
        $path = ltrim($path, '/');
        $path = explode('/', $path);
        $path = array_filter($path);
        $path = array_values($path);
        if (empty($path[1])) {
            return [];
        }
        $module     = $path[0] ?? '';
        $controller = $path[1] ?? '';
        $controller = ucfirst($controller);
        $action     = $path[2] ?? '';
        $suffix     = config('app.controller_suffix', '');
        $class      = "app\\{$module}\\controller\\{$controller}{$suffix}";
        if (!class_exists($class)) {
            throw new Exception("class not exists: {$class}");
        }
        return [
            'module' => $module,
            'controller' => $controller,
            'action' => $action,
            'path' => "{$module}/{$controller}/{$action}",
            'class' => $class,
        ];
    }
}