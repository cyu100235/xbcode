<?php

namespace app\common\model;

use app\common\Model;

class AdminRole extends Model
{
    // 设置JSON字段转换
    protected $json = ['rule'];
    // 设置JSON数据返回数组
    protected $jsonAssoc = true;

    /**
     * 获取管理员旗下角色
     * @param int $admin_id
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function selectOptions(int $admin_id): array
    {
        $where = [
            ['pid', '=', $admin_id],
        ];
        $field = [
            'id as value',
            'username as label'
        ];
        $list = self::where($where)
            ->field($field)
            ->select()
            ->toArray();
        return $list;
    }
}
