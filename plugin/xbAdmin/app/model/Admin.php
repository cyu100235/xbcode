<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\app\model;

use plugin\xbAdmin\utils\PasswdUtil;
use plugin\xbCode\app\Model;

/**
 * 管理员模型
 */
class Admin extends Model
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * 关联角色
     * @return \think\model\relation\HasOne
     */
    public function role()
    {
        return $this->hasOne(AdminRole::class, 'id', 'role_id');
    }

    /**
     * 设置登录密码
     * @param mixed $value
     * @return mixed
     */
    protected function setPasswordAttr($value)
    {
        if ($value) {
            $value = PasswdUtil::create((string) $value);
        }
        return $value;
    }
}
