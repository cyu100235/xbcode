<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @version  1.0
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Renders\Common;

/**
 * 组件辅助函数
 * @copyright 贵州猿创科技有限公司
 * @author 楚羽幽 416716328@qq.com
 */
trait ComponentUtils
{
    /**
     * 动态设置组件属性
     * @param mixed $component
     * @param array|callable $callback
     * @return void
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     */
    protected function setComponent(mixed $component, array|callable $callback = null)
    {
        // 回调函数设置属性
        if ($callback && is_callable($callback)) {
            $callback($component);
        }
        // 数组设置属性
        if ($callback && is_array($callback)) {
            foreach ($callback as $name => $value) {
                $component->setVariable($name, $value);
            }
        }
    }
}
