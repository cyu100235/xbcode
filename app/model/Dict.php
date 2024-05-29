<?php

namespace app\model;

use app\common\Model;
use think\facade\Cache;

/**
 * 字典模型
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class Dict extends Model
{
    /**
     * 缓存所有字典数据
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function cacheDict()
    {
        $data = self::select()->toArray();
        Cache::set('dict_all', $data);
    }
}
