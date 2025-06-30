<?php
namespace plugin\xbCode\api;

use Exception;
use plugin\xbCode\app\model\Admin;
use plugin\xbCode\utils\PasswdUtil;
use plugin\xbCode\utils\TokenUtil;

/**
 * 管理员接口
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class AdminApi
{
    /**
     * 登录
     * @param Admin $model 登录模型
     * @throws \Exception
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private static function login(Admin $model)
    {
        if ($model['state'] == '10') {
            throw new Exception('该用户已被冻结');
        }
        // 更新登录信息
        $ip = request()->getLocalIp();
        $model->login_ip = $ip;
        $model->login_time = date('Y-m-d H:i:s');
        $model->save();
        // 处理用户信息
        $user = $model->toArray();
        unset($user['password']);
        // 设置登录数据
        request()->session()->set('xbcode', $user);
        // 生成令牌
        $data = TokenUtil::create($user);
        // 返回登录数据
        return $data;
    }

    /**
     * 账号登录
     * @param string $username 登录账号
     * @param string $password 登录密码
     * @param string $captcha 验证码
     * @throws \Exception
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function accountLogin(string $username, string $password, string $captcha = null)
    {
        // 是否开启验证码
        // $isVcode = AppsEntry::get()['public_api']['captcha'] ?? '';
        $isVcode = '';
        if ($isVcode && class_exists('Webman\Captcha\CaptchaBuilder')) {
            // 验证码效验
            if (empty($captcha)) {
                throw new Exception('请填写验证码');
            }
            $captcha = strtolower($captcha);
            $session = request()->session()->get('captcha');
            if ($captcha !== $session) {
                throw new Exception('验证码错误');
            }
        }
        $model = Admin::where('username', $username)->find();
        if (empty($model)) {
            throw new Exception('登录账号错误');
        }
        // 验证登录密码
        $password = PasswdUtil::create($password);
        $originPwd = (string) $model['password'];
        if ($password !== $originPwd) {
            throw new Exception('登录密码错误');
        }
        $data = static::login($model);
        // 设置登录数据
        $user = request()->session()->get('xbcode');
        // 设置登录日志数据
        request()->uid = $user['id'];
        request()->username = $user['username'];
        request()->user = $user;
        // 返回登录数据
        return $data;
    }

    /**
     * 系统登录
     * @param int $id
     * @throws \Exception
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function systemLogin(int $id)
    {
        $where = [
            'saas_appid' => $id,
            'is_system' => '20',
        ];
        $model = Admin::withoutGlobalScope(['appid'])->where($where)->find();
        if (empty($model)) {
            throw new Exception('登录账号错误');
        }
        $data = static::login($model);
        // 返回登录数据
        return $data;
    }
}