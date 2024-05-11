<?php

namespace app\model;

use app\Model;
use app\service\UploadService;
use app\utils\PasswordUtil;
use app\utils\Settings;
use Exception;
use loong\oauth\facade\Auth;
use think\model\concern\SoftDelete;

/**
 * 用户模型
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class User extends Model
{
    // 软删除
    use SoftDelete;
    protected $deleteTime = 'delete_time';

    /**
     * 数据类型转换
     * @var array
     */
    protected $type = [
        'balance' => 'float',
    ];

    /**
     * 获取用户信息
     * @param array $where
     * @param mixed $hidden
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function userInfo(array $where, mixed $hidden = '')
    {
        return self::where($where)->withoutField($hidden)->find();
    }

    /**
     * 获取当前用户令牌解密数据
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getTokenUser()
    {
        $authorization = request()->header('Authorization', '');
        if (empty($authorization)) {
            throw new Exception('请先登录', 12000);
        }
        // 获取用户信息
        try {
            $token = str_replace('Bearer ', '', $authorization);
            $user  = Auth::setExpire(3600 * 24 * 7)->decrypt($token);
        } catch (\Throwable $e) {
            throw new Exception('登录已过期，请重新登录', 12000);
        }
        if (!$user) {
            throw new Exception('用户信息获取失败', 12000);
        }
        return $user;
    }

    /**
     * 设置头像
     * @param mixed $value
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function setAvatarAttr($value)
    {
        if ($value) {
            $value = UploadService::path($value);
        }
        return $value;
    }

    /**
     * 获取头像
     * @param mixed $value
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function getAvatarAttr($value)
    {
        if ($value) {
            $value = UploadService::url($value);
        }
        // 设置默认头像
        $defaultAvatar = Settings::config('user', 'default_avatar');
        if ($defaultAvatar && !$value) {
            $value = $defaultAvatar;
        }
        return $value;
    }

    /**
     * 设置密码
     * @param mixed $value
     * @return string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function setPasswordAttr($value)
    {
        if ($value) {
            $value = PasswordUtil::passwordHash($value);
        }
        return $value;
    }

    /**
     * 获取用户余额
     * @param int $uid
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getBbalance(int $uid)
    {
        return self::where('id', $uid)->value('balance', 0);
    }

    /**
     * 获取用户选项
     * @return array|\think\Collection
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getOptions()
    {
        $field = 'id,username,nickname';
        $data  = self::order('id desc')
            ->field($field)
            ->order('id desc')
            ->select()
            ->each(function ($model) {
                $model->label = "UID:{$model['id']}--{$model['username']}--{$model['nickname']}";
                $model->value = $model->id;
            })
            ->toArray();
        return $data;
    }
}
