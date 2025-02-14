<?php

namespace plugin\xbCode\app\model;

use plugin\xbCode\api\Plugins;
use plugin\xbCode\Model;
use plugin\xbCode\utils\PasswdUtil;
use plugin\xbCode\providers\FileProvider;

class Admin extends Model
{
    /**
     * 关联角色
     * @return \think\model\relation\HasOne
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function role()
    {
        return $this->hasOne(AdminRole::class, 'id', 'role_id');
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
        if ($value && Plugins::checked('xbUpload')) {
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
        if ($value && Plugins::checked('xbUpload')) {
            $value = FileProvider::url($value);
        }
        return $value;
    }

    /**
     * 设置密码
     * @param mixed $value
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function setPasswordAttr($value)
    {
        if ($value) {
            $value = PasswdUtil::create($value);
        }
        return $value;
    }
}
