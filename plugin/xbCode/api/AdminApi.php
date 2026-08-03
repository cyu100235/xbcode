<?php
/**
 * 积木云渲染器
 * @package  XbCode.0
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\api;

use Exception;
use support\think\Cache;
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
     * 账号登录
     * @param string $username 登录账号
     * @param string $password 登录密码
     * @param string $captcha 验证码
     * @throws \Exception
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function username(string $username, string $password, ?string $captcha = null)
    {
        // 是否开启验证码
        $isCatch = ConfigApi::make('system')->get('captcha_state', '10');
        if ($isCatch === '20' && class_exists('Webman\Captcha\CaptchaBuilder')) {
            // 验证码效验
            if (empty($captcha)) {
                throw new Exception('请填写验证码');
            }
            $captcha = strtolower($captcha);
            $sessionId = request()->session()->getId();
            $sessionCaptcha = Cache::get($sessionId);
            if ($captcha !== $sessionCaptcha) {
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
        // 生成令牌
        $data = TokenUtil::create($user);
        // 设置登录日志数据
        request()->uid = $user['id'];
        request()->username = $user['username'];
        request()->user = $user;
        // 返回登录数据
        return $data;
    }
}