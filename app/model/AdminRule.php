<?php

namespace app\model;

use xbcode\Model;

/**
 * 菜单规则
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class AdminRule extends Model
{
    /**
     * 设置请求类型数据
     * @param mixed $value
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function setMethodAttr($value)
    {
        if (is_array($value)) {
            $value = implode(',', $value);
        }
        return $value;
    }

    /**
     * 获取请求类型数据
     * @param mixed $value
     * @return string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function getMethodAttr($value)
    {
        if ($value) {
            $value = explode(',', $value);
        }
        return $value;
    }

    /**
     * 设置路径数据
     * @param mixed $value
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function setPathAttr($value)
    {
        // 检测是否有斜杠
        if (strpos($value, '/') !== false) {
            $temp = explode('/', $value);
            // 原控制器名称
            $controller = $temp[1] ?? '';
            // 新控制器名称
            $control = ucfirst($controller);
            // 替换控制器名称
            $value = str_replace($controller, $control, $value);
        }
        return $value;
    }
}
