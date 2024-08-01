<?php

namespace app\model;

use app\common\Model;

/**
 * 菜单模型
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class AdminRule extends Model
{
    /**
     * 设置模块名称数据
     * @param mixed $value
     * @return string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function setModuleNameAttr($value)
    {
        return strtolower($value);
    }

    /**
     * 设置路由地址数据
     * @param mixed $value
     * @return string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function setPathAttr($value)
    {
        return ucfirst($value);
    }
}
