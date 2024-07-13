<?php
namespace app\common\providers;

/**
 * 事件服务提供者
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class EventProvider
{
    /**
     * 初始化事件
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function init()
    {
        // 框架文件路径
        $paths = glob(base_path('app/**/event/*Event.php'));
        // 插件文件路径
        $pluginPaths = glob(base_path('plugin/**/app/event/*Event.php'));
        // 框架事件
        $appEvents = self::load($paths);
        // 插件事件
        $pluginEvents = self::load($pluginPaths);
        // 加载自定义事件
        $customEvents = self::customEvent();
        // 合并事件
        $events = array_merge($appEvents, $pluginEvents, $customEvents);
        // 导出事件
        return $events;
    }

    /**
     * 加载自定义事件
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function customEvent()
    {
        $paths = [
            base_path('app/**/events.php'),
            base_path('plugin/**/app/events.php'),
            base_path('plugin/**/app/**/events.php'),
        ];
        $data  = [];
        foreach ($paths as $matchPath) {
            $pathRes = glob($matchPath);
            if (empty($pathRes)) {
                continue;
            }
            foreach ($pathRes as $path) {
                $event = require $path;
                if (empty($event)) {
                    continue;
                }
                $data = array_merge($data, $event);
            }
        }
        return $data;
    }

    /**
     * 加载事件
     * @param array $events
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function load(array $events)
    {
        $basePath = base_path();
        $data     = [];
        foreach ($events as $path) {
            $shortPath = str_replace($basePath, '', $path);
            $shortPath = str_replace('.php', '', $shortPath);
            $shortPath = str_replace('/', '\\', $shortPath);
            if (!class_exists($shortPath)) {
                continue;
            }
            $class = new \ReflectionClass($shortPath);
            // 反射类公有方法
            $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
            foreach ($methods as $value) {
                $name     = str_replace('\\', '.', $value->class);
                $name     = str_replace('app.', '', $name);
                $name     = str_replace('plugin.', '', $name);
                $fullName = "{$name}.{$value->name}";
                $item     = [
                    [
                        $value->class,
                        $value->name,
                    ]
                ];
                // 追加事件
                $data[$fullName] = $item;
            }
        }
        return $data;
    }
}