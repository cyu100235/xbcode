<?php
namespace app\common\providers;
use Exception;

/**
 * 插槽提供者
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class SlotProvider
{
    /**
     * 监听器
     * @var array
     */
    private static $listeners = [];
    
    /**
     * 加载监听器
     * @param array $data
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function load(array $data)
    {
        if (empty($data)) {
            return;
        }
        foreach ($data as $name => $value) {
            if (empty($value)) {
                continue;
            }
            if (!is_callable($value)) {
                throw new Exception('监听器必须是可调用的');
            }
            if (empty(static::$listeners[$name])) {
                static::$listeners[$name] = [$value];
            } else {
                array_push(static::$listeners[$name], $value);
            }
        }
    }

    /**
     * 获取监听器列表
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function list()
    {
        return static::$listeners;
    }
    
    /**
     * 触发监听器
     * @param string $name
     * @param string $type
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function trigger(string $name,string $type = 'html')
    {
        $data = self::list();
        if (!isset($data[$name])) {
            throw new Exception('监听器不存在');
        }
        $listener = $data[$name];
        if ($type === 'html') {
            $render = end($listener);
            return call_user_func($render);
        }
        foreach ($listener as $item) {
            call_user_func($item);
        }
    }
}