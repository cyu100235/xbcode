<?php

namespace app\model;

use xbcode\Model;
use xbcode\providers\FileProvider;
use xbcode\utils\PasswdUtil;

/**
 * 站点管理员
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class WebAdmin extends Model
{
    /**
     * 关联角色
     * @return \think\model\relation\HasOne
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function role()
    {
        return $this->hasOne(WebRole::class, 'id', 'role_id');
    }

    /**
     * 设置头像地址
     * @param mixed $value
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function setAvatarAttr($value)
    {
        if ($value) {
            $value = FileProvider::path($value);
        }
        return $value;
    }

    /**
     * 获取头像地址
     * @param mixed $value
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function getAvatarAttr($value)
    {
        if ($value) {
            $value = FileProvider::url($value);
        }
        return $value;
    }

    /**
     * 设置登录密码
     * @param mixed $value
     * @return string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function setPasswordAttr($value)
    {
        if (empty($value)) {
            throw new \Exception('登录密码参数错误');
        }
        return PasswdUtil::create($value);
    }
}
