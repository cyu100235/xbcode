<?php

namespace app\model;

use app\common\Model;
use app\providers\UploadProvider;
use app\common\utils\PasswordUtil;

/**
 * 管理员
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class Admin extends Model
{
    // 隐藏字段
    protected $hidden = [
        'password'
    ];
    
    /**
     * 密码加密写入
     * @param mixed $value
     * @return bool|string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function setPasswordAttr($value)
    {
        if (!$value) {
            return false;
        }
        return PasswordUtil::passwordHash((string)$value);;
    }

    /**
     * 设置头像储存
     * @param mixed $value
     * @return array|string
     * @copyright 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-05-03
     */
    protected function setAvatarAttr($value)
    {
        return UploadProvider::path($value);
    }
    
    /**
     * 关联角色
     * @return \think\model\relation\HasOne
     * @copyright 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-04-29
     */
    public function role()
    {
        return $this->hasOne(AdminRole::class, 'id', 'role_id');
    }

    /**
     * 关联角色权限
     * @return \think\model\relation\HasOne
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function rule()
    {
        return $this->hasOne(AdminRole::class, 'id', 'role_id')->field('rule');
    }

    /**
     * 获取管理员选项
     * @param int $admin_id
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getOptions(int $admin_id): array
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
