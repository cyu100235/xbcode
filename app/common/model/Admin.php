<?php

namespace app\common\model;

use app\common\Model;
use app\common\service\UploadService;
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
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
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
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-03
     */
    protected function setAvatarAttr($value)
    {
        return UploadService::path($value);
    }
    
    /**
     * 关联等级
     * @return \think\model\relation\HasOne
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function role()
    {
        return $this->hasOne(AdminRole::class, 'id', 'role_id');
    }

    /**
     * 关联项目
     * @return \think\model\relation\HasOne
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function project()
    {
        return $this->hasOne(Projects::class, 'id', 'saas_appid');
    }
    
    /**
     * 获取管理员组件选项
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-09
     * @param  integer $admin_id
     * @return array
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

    /**
     * 获取角色组件选项
     * @param int $admin_id
     * @return array
     * @author John
     */
    public static function selectRoleOptions(int $admin_id): array
    {
        $list  = AdminRole::selectOptions($admin_id);
        return $list;
    }
}
