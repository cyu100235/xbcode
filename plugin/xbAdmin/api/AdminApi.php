<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\api;

use plugin\xbAdmin\app\model\Admin;
use plugin\xbAdmin\enum\StateEnum;
use plugin\xbAdmin\exception\business\ExceptionBusiness;
use plugin\xbAdmin\exception\business\ExceptionUnauthorized;
use plugin\xbAdmin\utils\PasswdUtil;
use plugin\xbAdmin\utils\TokenUtil;

/**
 * 管理员账号接口
 */
class AdminApi
{
    /**
     * 验证码存放的会话键
     */
    public const CAPTCHA_KEY = 'xbadmin_captcha';

    /**
     * 账号密码登录
     * @param string $username 登录账号
     * @param string $password 登录密码
     * @param string|null $captcha 验证码
     * @throws ExceptionBusiness
     * @throws ExceptionUnauthorized
     * @return array
     */
    public static function username(string $username, string $password, ?string $captcha = null): array
    {
        static::checkCaptcha($captcha);
        $username = trim($username);
        if ($username === '') {
            throw new ExceptionBusiness('请填写登录账号');
        }
        $model = Admin::where('username', $username)->find();
        if (empty($model)) {
            throw new ExceptionBusiness('登录账号错误');
        }
        if (!PasswdUtil::check($password, (string) $model['password'])) {
            throw new ExceptionBusiness('登录密码错误');
        }
        if ((string) $model['state'] === StateEnum::DISABLED['value']) {
            throw new ExceptionBusiness('该用户已被冻结，请联系管理员');
        }
        // 记录登录信息
        $model->login_ip = request()->getRealIp();
        $model->login_time = date('Y-m-d H:i:s');
        $model->save();
        $user = $model->toArray();
        unset($user['password']);
        // 登录成功后不再保留验证码
        request()->session()->forget(static::CAPTCHA_KEY);
        // 挂载当前登录信息，供中间件之后的业务逻辑使用
        request()->uid = $user['id'];
        request()->username = $user['username'] ?? '';
        request()->user = $user;
        return TokenUtil::create($user);
    }

    /**
     * 刷新登录令牌
     * @throws ExceptionUnauthorized
     * @return array
     */
    public static function refreshToken(): array
    {
        return TokenUtil::refreshToken();
    }

    /**
     * 退出登录
     * @return bool
     */
    public static function logout(): bool
    {
        return TokenUtil::logout();
    }

    /**
     * 是否开启登录验证码
     * @return bool
     */
    public static function captchaState(): bool
    {
        return (string) xbAdminConfig('xbadmin.captcha_state', StateEnum::DISABLED['value']) === StateEnum::ENABLED['value'];
    }

    /**
     * 校验登录验证码，未开启验证码时直接通过
     * @param string|null $captcha
     * @throws ExceptionBusiness
     * @return void
     */
    public static function checkCaptcha(?string $captcha): void
    {
        if (!static::captchaState()) {
            return;
        }
        $input = trim((string) $captcha);
        if ($input === '') {
            throw new ExceptionBusiness('请填写验证码');
        }
        $session = request()->session();
        $expect = (string) $session->pull(static::CAPTCHA_KEY, '');
        if ($expect === '' || !hash_equals(strtolower($expect), strtolower($input))) {
            throw new ExceptionBusiness('验证码错误或已失效，请重新获取');
        }
    }
}
