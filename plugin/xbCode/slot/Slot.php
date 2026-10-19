<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\slot;

use Throwable;
use support\Log;
use support\Container;
use plugin\xbCode\api\PluginsApi;

/**
 * 通用插槽注册表
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class Slot
{
    /**
     * 已注册的插槽监听器
     * @var array<string, array<int, array{handler:callable|array, target:string, after:bool, plugin:string}>>
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static array $listeners = [];

    /**
     * 监听器自增编号
     * @var int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static int $increment = 0;

    /**
     * 注册插槽监听器
     * @param string $slot 插槽名称
     * @param string $targetColumn 目标表格列键名，为空表示不限制插入位置
     * @param callable|array $handler 处理回调，支持 [类名::class, '方法']
     * @param bool $after 是否插入到目标列之后，默认 true（后面）
     * @return int 监听器编号
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function listen(string $slot, string $targetColumn, callable|array $handler, bool $after = true): int
    {
        $id = ++static::$increment;
        static::$listeners[$slot][$id] = [
            'handler' => $handler,
            'target'  => $targetColumn,
            'after'   => $after,
            'plugin'  => static::resolvePlugin($handler),
        ];
        return $id;
    }

    /**
     * 移除插槽监听器
     * @param string $slot 插槽名称
     * @param int $id 监听器编号
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function forget(string $slot, int $id): void
    {
        unset(static::$listeners[$slot][$id]);
    }

    /**
     * 判断插槽是否存在可用监听器
     * @param string $slot 插槽名称
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function has(string $slot): bool
    {
        return !empty(static::enabledListeners($slot));
    }

    /**
     * 派发插槽
     * @param string $slot 插槽名称
     * @param object $payload 可变上下文对象，监听器直接修改该对象状态
     * @return object 返回传入的上下文对象
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function dispatch(string $slot, object $payload): object
    {
        // 上下文是否支持表格列定位（如 XbCrud）
        $columnAware = method_exists($payload, 'getSlotColumnCount') && method_exists($payload, 'applySlotColumn');
        foreach (static::enabledListeners($slot) as $listener) {
            try {
                $addedFrom = $columnAware ? $payload->getSlotColumnCount() : null;
                $handler = static::resolve($listener['handler']);
                $handler($payload, $slot);
                // 将本次回调追加的列移动到目标列的前/后
                if ($columnAware && $listener['target'] !== '') {
                    $payload->applySlotColumn($addedFrom, $listener['target'], $listener['after']);
                }
            } catch (Throwable $e) {
                Log::error(sprintf('插槽[%s]执行异常：%s', $slot, $e->getMessage()), [
                    'slot'   => $slot,
                    'file'   => $e->getFile(),
                    'line'   => $e->getLine(),
                ]);
            }
        }
        return $payload;
    }

    /**
     * 获取插槽监听器（按注册顺序）
     * @param string $slot 插槽名称
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getListeners(string $slot): array
    {
        $listeners = static::$listeners[$slot] ?? [];
        if (empty($listeners)) {
            return [];
        }
        $items = [];
        foreach ($listeners as $id => $listener) {
            $listener['id'] = $id;
            $items[] = $listener;
        }
        return $items;
    }

    /**
     * 获取可用的插槽监听器（仅保留已安装且已启用插件注册的监听器）
     * @param string $slot 插槽名称
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function enabledListeners(string $slot): array
    {
        $items = [];
        $states = [];
        foreach (static::getListeners($slot) as $listener) {
            $plugin = $listener['plugin'] ?? '';
            if (!array_key_exists($plugin, $states)) {
                $states[$plugin] = static::isPluginEnabled($plugin);
            }
            if ($states[$plugin]) {
                $items[] = $listener;
            }
        }
        return $items;
    }

    /**
     * 判断监听器归属插件是否已安装并启用（插件未安装、已禁用时插槽不生效）
     * @param string $plugin 插件标识，为空表示无法归属插件，不做拦截
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function isPluginEnabled(string $plugin): bool
    {
        if ($plugin === '') {
            return true;
        }
        try {
            $api = PluginsApi::make();
            return $api->installed($plugin) && $api->hasEnabled($plugin);
        } catch (Throwable $e) {
            Log::error(sprintf('插槽插件状态检测异常：%s', $e->getMessage()), [
                'plugin' => $plugin,
                'file'   => $e->getFile(),
                'line'   => $e->getLine(),
            ]);
            return false;
        }
    }

    /**
     * 解析监听器归属的插件标识
     * @param callable|array $handler
     * @return string 插件标识，无法确定时返回空字符串
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function resolvePlugin(callable|array $handler): string
    {
        $class = '';
        if (is_array($handler) && isset($handler[0]) && is_string($handler[0])) {
            $class = $handler[0];
        } elseif (is_string($handler) && str_contains($handler, '::')) {
            $class = explode('::', $handler, 2)[0];
        } elseif (is_object($handler)) {
            $class = get_class($handler);
        }
        // 优先按类命名空间解析插件标识（plugin\{插件名}\...）
        if (str_starts_with($class, 'plugin\\')) {
            $parts = explode('\\', $class);
            return $parts[1] ?? '';
        }
        // 闭包等场景按定义文件路径解析（plugin/{插件名}/...）
        try {
            if ($handler instanceof \Closure) {
                $file = (string) (new \ReflectionFunction($handler))->getFileName();
            } elseif ($class !== '' && class_exists($class) && method_exists($class, '__invoke')) {
                $file = (string) (new \ReflectionMethod($class, '__invoke'))->getFileName();
            } else {
                $file = '';
            }
        } catch (Throwable) {
            $file = '';
        }
        $file = str_replace('\\', '/', $file);
        if (preg_match('#/plugin/([^/]+)/#', $file, $matches)) {
            return $matches[1];
        }
        return '';
    }

    /**
     * 解析监听器回调（支持 [类名::class, '方法'] 实例化）
     * @param callable|array $handler
     * @return callable
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function resolve(callable|array $handler): callable
    {
        if (is_array($handler) && isset($handler[1]) && is_string($handler[0]) && class_exists($handler[0])) {
            return [Container::get($handler[0]), $handler[1]];
        }
        return $handler;
    }
}