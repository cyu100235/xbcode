<?php
namespace plugin\{PLUGIN_NAME}\app\event;

/**
 * 事件类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class {CLASS_NAME}
{
    /**
     * 添加事件
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function add($data)
    {
        // 触发事件执行成功事件
        $data = [];
        Event::dispatch('{PLUGIN_NAME}.event.{CLASS_NAME}.add.success', $data);
    }
}