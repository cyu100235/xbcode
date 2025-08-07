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
namespace plugin\xbCode\app\model;

use support\think\Cache;
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
        // 全部转大写
        $value = ucfirst($value);
        // 返回数据
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
        $data = static::column('*', 'path');
        $list = [];
        foreach ($data as $key => $value) {
            if (isset($value['plugin']) && $value['plugin']) {
                $value['path'] = "app/{$value['plugin']}/{$value['path']}";
            }
            $list[$value['path']] = $value;
        }
        Cache::set($key, $list, 600);
        return $list;
    }
}
