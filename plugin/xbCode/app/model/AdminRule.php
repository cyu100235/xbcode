<?php

namespace plugin\xbCode\app\model;

use support\Cache;
use plugin\xbCode\Model;

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
     * 获取菜单字典
     * @param bool $force 是否强制刷新
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getMenuDict(bool $force = false)
    {
        $key = 'menu_title_dict';
        $data = Cache::get($key);
        if ($data && !$force) {
            return $data;
        }
        $data = self::column('*', 'path');
        Cache::set($key, $data, 600);
        return $data;
    }
}
