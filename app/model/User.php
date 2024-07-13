<?php
namespace app\model;

use app\common\Model;
use app\common\providers\UploadProvider;
use app\common\utils\PasswordUtil;

/**
 * 用户模型
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class User extends Model
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
        return PasswordUtil::passwordHash((string) $value);
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
        if ($value) {
            $value = UploadProvider::path($value);
        }
        return $value;
    }

    /**
     * 获取头像储存
     * @param mixed $value
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function getAvatarAttr($value)
    {
        if ($value) {
            $value = UploadProvider::url($value);
        }
        return $value;
    }
}
