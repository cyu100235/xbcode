<?php
namespace app\common\providers;

/**
 * 中间件服务提供者
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class MiddlewareProvider
{
    /**
     * 初始化中间件
     * @param string $plugin
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function init(string $plugin)
    {
        $sitePath = base_path();
        $appPath = "{$sitePath}/plugin/{$plugin}/app/**/middleware.php";
        $middFiles = glob($appPath);
        $middPath = "{$sitePath}/plugin/{$plugin}/app/middleware.php";
        if (file_exists($middPath)) {
            $middFiles[] = $middPath;
        }
        // 处理数据
        $data = [];
        foreach ($middFiles as $value) {
            // 获取名称
            $name = dirname($value);
            $name = str_replace("{$sitePath}/", '', $name);
            $name = str_replace('/app/', '/', $name);
            $name = str_replace('/app', '', $name);
            $name = str_replace('/', '.', $name);
            // 获取中间件文件
            $midd = require $value;
            $data[$name] = $midd;
        }
        return $data;
    }
}