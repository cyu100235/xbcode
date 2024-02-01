<?php

/**
 * 渲染视图
 * @param string $dirname
 * @param string $xbaseName
 * @param string $file
 * @throws \Exception
 * @return string
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
function getViewContent(string $dirname = '',string $xbaseName = '',string $file = 'index.html')
{
    $dirname = $dirname? "{$dirname}/" : '';
    if ($xbaseName) {
        $xbaseRootPath = request()->xBaseRootPath;
        $filePath = "{$xbaseRootPath}{$xbaseName}/public/{$dirname}{$file}";
    } else {
        $rootPath = root_path();
        $filePath = "{$rootPath}public/{$dirname}{$file}";
    }
    if (!file_exists($filePath)) {
        throw new \Exception("文件不存在:{$filePath}");
    }
    return \think\facade\View::fetch($filePath);
}

/**
 * 验证器
 * @param mixed $validate
 * @param array $data
 * @param string $scene
 * @return bool
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
function xbValidate($validate, array $data, string $scene = ''): bool
{
    // 实例类
    $class = new $validate;
    // 场景验证
    if ($scene) {
        $class->scene($scene);
    }
    $result = $class->check($data);
    if (!$result) {
        throw new Exception((string) $class->getError(),404);
    }
    return true;
}


/**
 * 友好时间显示
 * @param int $time
 * @return bool|string
 * @author 贵州小白基地网络科技有限公司
 * @copyright 贵州小白基地网络科技有限公司
 */
function friend_date(int $time)
{
    if (!$time)
        return false;
    $fdate = '';
    $d     = time() - intval($time);
    $ld    = $time - mktime(0, 0, 0, 0, 0, date('Y')); //得出年
    $md    = $time - mktime(0, 0, 0, date('m'), 0, date('Y')); //得出月
    $byd   = $time - mktime(0, 0, 0, date('m'), date('d') - 2, date('Y')); //前天
    $yd    = $time - mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')); //昨天
    $dd    = $time - mktime(0, 0, 0, date('m'), date('d'), date('Y')); //今天
    $td    = $time - mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')); //明天
    $atd   = $time - mktime(0, 0, 0, date('m'), date('d') + 2, date('Y')); //后天
    if ($d == 0) {
        $fdate = '刚刚';
    } else {
        switch ($d) {
            case $d < $atd:
                $fdate = date('Y年m月d日', $time);
                break;
            case $d < $td:
                $fdate = '后天' . date('H:i', $time);
                break;
            case $d < 0:
                $fdate = '明天' . date('H:i', $time);
                break;
            case $d < 60:
                $fdate = $d . '秒前';
                break;
            case $d < 3600:
                $fdate = floor($d / 60) . '分钟前';
                break;
            case $d < $dd:
                $fdate = floor($d / 3600) . '小时前';
                break;
            case $d < $yd:
                $fdate = '昨天' . date('H:i', $time);
                break;
            case $d < $byd:
                $fdate = '前天' . date('H:i', $time);
                break;
            case $d < $md:
                $fdate = date('m月d日 H:i', $time);
                break;
            case $d < $ld:
                $fdate = date('m月d日 H:i', $time);
                break;
            default:
                $fdate = date('Y年m月d日', $time);
                break;
        }
    }
    return $fdate;
}
/**
 * 生成6位随机数
 * @param int $len
 * @return string
 * @author 贵州小白基地网络科技有限公司
 * @copyright 贵州小白基地网络科技有限公司
 */
function get_random(int $len = 6)
{
    $unique_no = substr(base_convert(md5(uniqid(md5(microtime(true)), true)), 16, 10), 0, $len);
    return $unique_no;
}
/**
 * XML转数组
 *
 * @Author 贵州小白基地网络科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-12
 * @param  string $xml
 * @return array
 */
function xmlToArr(string $xml): array
{
    //将xml转化为json格式
    $jsonxml = json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA));
    //转成数组
    $result = json_decode($jsonxml, true);

    // 返回数组
    return $result;
}

/**
 * 根据大小返回标准单位 KB  MB GB等
 *
 * @Author 贵州小白基地网络科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-12
 * @param  integer $size
 * @param  integer $decimals
 * @return string
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
 * 下划线转驼峰
 *
 * step1.原字符串转小写,原字符串中的分隔符用空格替换,在字符串开头加上分隔符
 * step2.将字符串中每个单词的首字母转换为大写,再去空格,去字符串首部附加的分隔符.
 * @param string $uncamelized_words
 * @param string $separator
 * @return string
 */
function camelize(string $uncamelized_words, $separator = '_'): string
{
    $uncamelized_words = $separator . str_replace($separator, " ", strtolower($uncamelized_words));
    return ltrim(str_replace(" ", "", ucwords($uncamelized_words)), $separator);
}
/**
 * 驼峰命名转下划线命名
 *
 * 小写和大写紧挨一起的地方,加上分隔符,然后全部转小写
 * @param string $camelCaps
 * @param string $separator
 * @return string
 */
function uncamelize(string $camelCaps, $separator = '_'): string
{
    return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
}

/**
 * 对查询结果集进行排序
 * asc正向排序 desc逆向排序 nat自然排序
 * @param mixed $list 查询结果
 * @param mixed $field 排序的字段名
 * @param mixed $sortby 排序类型
 * @return array|bool
 * @copyright 贵州小白基地网络科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-05-03
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
 * 获取模块路由
 * @return string
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
function getModuleRoute()
{
    $request = request();
    if (empty($request->xBaseName)) {
        # 设置总后台路由格式
        $moduleRoute = app('http')->getName();
        $moduleRoute = "/{$moduleRoute}";
    } else {
        # 设置应用后台路由格式
        $appName = $request->xBaseName;
        $moduleName = $request->xModuleName;
        $moduleRoute = "/base/{$appName}/{$moduleName}";
    }
    return $moduleRoute;
}

/**
 * 获取应用路由
 * @return string
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
function getBaseRoute()
{
    $request = request();
    if (empty($request->xBaseName)) {
        # 设置总后台路由格式
        $moduleRoute = app('http')->getName();
        $route = "/{$moduleRoute}";
    } else {
        # 设置应用后台路由格式
        $appName = $request->pathinfo();
        $data = explode('/', $appName);
        if (!isset($data[3])) {
            throw new Exception('应用路由错误');
        }
        $route = "/{$data[0]}/{$data[1]}/{$data[2]}";
    }
    return $route;
}