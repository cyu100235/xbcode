<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\app\model;

use plugin\xbCode\Model;
use plugin\xbUpload\api\Files;
use plugin\xbCode\api\AdminApi;
use plugin\xbCode\api\PluginsApi;
use plugin\xbCode\utils\PasswdUtil;

class Admin extends Model
{
    /**
     * 关联角色
     * @return \think\model\relation\HasOne
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function role()
    {
        return $this->hasOne(AdminRole::class, 'id', 'role_id');
    }

    /**
     * 设置头像地址
     * @param mixed $value
     * @return mixed
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function setAvatarAttr($value)
    {
        if ($value && PluginsApi::make()->exists('xbUpload')) {
            $value = Files::make()->path($value);
        }
        return $value;
    }

    /**
     * 获取头像地址
     * @param mixed $value
     * @return mixed
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getAvatarAttr($value)
    {
        if ($value && PluginsApi::make()->exists('xbUpload')) {
            $value = Files::make()->url($value);
        }
        return $value;
    }

    /**
     * 设置密码
     * @param mixed $value
     * @return mixed
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function setPasswordAttr($value)
    {
        if ($value) {
            $value = PasswdUtil::create($value);
        }
        return $value;
    }

    /**
     * 写入后同步缓存
     * 1、清理该管理员的信息缓存，资料（昵称/头像/角色等）修改后立即生效
     * 2、密码变更时刷新密码指纹缓存，旧令牌在下一次请求即被中间件判定失效
     * @param mixed $model
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function onAfterWrite($model)
    {
        $uid = $model->getKey();
        AdminApi::forgetUserinfo($uid);
        // 未提交密码或密码未变更时跳过；此时模型数据已是修改器处理后的密文
        if (!$model->isChange('password')) {
            return;
        }
        $password = $model->getData('password');
        if (empty($password)) {
            return;
        }
        AdminApi::setPasswordSign($uid, AdminApi::passwordSign((string) $password));
    }

    /**
     * 删除后清理缓存
     * @param mixed $model
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function onAfterDelete($model)
    {
        $uid = $model->getKey();
        AdminApi::forgetUserinfo($uid);
        AdminApi::forgetPasswordSign($uid);
    }
}
