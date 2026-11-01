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
use plugin\xbCode\utils\TokenUtil;
use plugin\xbCode\utils\IpUtil;
use plugin\xbCode\utils\PasswdUtil;
use plugin\xbCode\app\model\AdminRole;
use plugin\xbCode\app\model\AdminRule;
use plugin\xbCode\exception\business\ExceptionUnauthorized;

/**
 * 管理员接口
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class AdminApi
{
    /**
     * 密码指纹缓存有效期（秒）
     * @var int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public const PASSWORD_SIGN_TTL = 604800;

    /**
     * 管理员信息缓存有效期（秒）
     * 资料或角色变更时会主动清理，此处仅作为异常场景的兜底过期时间
     * @var int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public const USERINFO_TTL = 600;

    /**
     * 账号登录
     * @param string $username 登录账号
     * @param string $password 登录密码
     * @param string $captcha 验证码
     * @throws Exception
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
        // 密码指纹，供鉴权中间件识别密码是否已被修改
        $tokenUser = $user;
        $tokenUser['pwd_sign'] = self::passwordSign($user['password'] ?? '');
        unset($user['password'], $tokenUser['password']);
        // 登录时顺手回填指纹缓存，后续请求由中间件直接读缓存
        self::setPasswordSign((int) $user['id'], $tokenUser['pwd_sign']);
        // 登记登录会话，供「登录令牌」查询与踢出登录使用
        $session = bin2hex(random_bytes(16));
        $roleId = (int) ($user['role_id'] ?? 0);
        $role = AdminRole::where('id', $roleId)->field('title,is_single_login')->find();
        $single = (string) ($role['is_single_login'] ?? '10') === '20';
        // 预热角色单点登录配置缓存，鉴权中间件无需再查库
        AdminSession::setRoleSingleLogin($roleId, $single);
        $tokenUser['session'] = $session;
        // 单点登录角色会挤掉该账号的其它会话
        AdminSession::register([
            'session' => $session,
            'uid' => (int) $user['id'],
            'username' => (string) $user['username'],
            'nickname' => (string) ($user['nickname'] ?? ''),
            'role_id' => $roleId,
            'role_name' => (string) ($role['title'] ?? ''),
            'ip' => (string) $ip,
            'city' => IpUtil::getCity((string) $ip),
            'user_agent' => mb_substr((string) request()->header('user-agent', ''), 0, 500),
            'single' => $single,
            'login_time' => time(),
            'last_time' => time(),
            'expire_time' => time() + AdminSession::TTL,
        ]);
        // 生成令牌
        $data = TokenUtil::create($tokenUser);
        // 设置登录日志数据
        request()->uid = $user['id'];
        request()->username = $user['username'];
        request()->user = $user;
        // 返回登录数据
        return $data;
    }

    /**
     * 生成管理员密码指纹
     * 令牌中携带该指纹，密码被修改后已签发的令牌立即失效
     * @param string|null $password 数据库中的密码密文
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function passwordSign(?string $password): string
    {
        return substr(md5('xbcode|' . (string) $password), 0, 16);
    }

    /**
     * 密码指纹缓存键名
     * @param int|string $uid 管理员ID
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function passwordSignKey($uid): string
    {
        // 缓存驱动自身带前缀（如 REDIS_PREFIX），此处不再重复添加
        return "admin_pwd_sign_{$uid}";
    }

    /**
     * 写入密码指纹缓存
     * @param int|string $uid 管理员ID
     * @param string $sign 密码指纹
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function setPasswordSign($uid, string $sign)
    {
        Cache::set(self::passwordSignKey($uid), $sign, self::PASSWORD_SIGN_TTL);
    }

    /**
     * 删除密码指纹缓存（密码变更或账号删除时调用）
     * @param int|string $uid 管理员ID
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function forgetPasswordSign($uid)
    {
        Cache::delete(self::passwordSignKey($uid));
    }

    /**
     * 获取密码指纹
     * 优先读缓存（按 CACHE_TYPE 走 file 或 redis），未命中才回源数据库并回填，
     * 供鉴权中间件做逐请求校验，避免每次请求都查库
     * @param int|string $uid 管理员ID
     * @return string|null 管理员不存在时返回 null
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getPasswordSign($uid): ?string
    {
        if (empty($uid)) {
            return null;
        }
        $sign = Cache::get(self::passwordSignKey($uid));
        if ($sign !== null) {
            return (string) $sign;
        }
        $password = Admin::where('id', $uid)->value('password');
        if ($password === null) {
            return null;
        }
        $sign = self::passwordSign($password);
        self::setPasswordSign($uid, $sign);
        return $sign;
    }

    /**
     * 获取管理员信息
     * 结果按管理员ID缓存（按 CACHE_TYPE 走 file 或 redis），
     * 资料 / 角色变更时由 forgetUserinfo 主动清理，避免逐请求查库
     * @param int $uid 管理员ID
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function userinfo(int $uid)
    {
        if (empty($uid)) {
            throw new ExceptionUnauthorized('当前尚未登录，请先登录');
        }
        $data = Cache::get(self::userinfoKey($uid));
        if (is_array($data) && !empty($data)) {
            return $data;
        }
        $data = self::buildUserinfo($uid);
        Cache::set(self::userinfoKey($uid), $data, self::USERINFO_TTL);
        return $data;
    }

    /**
     * 管理员信息缓存键名
     * @param int|string $uid 管理员ID
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function userinfoKey($uid): string
    {
        // 缓存驱动自身带前缀（如 REDIS_PREFIX），此处不再重复添加
        return "admin_userinfo_{$uid}";
    }

    /**
     * 清理管理员信息缓存
     * 资料、角色或权限发生变更时调用，缓存下次访问时自动重建
     * @param int|string $uid 管理员ID
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function forgetUserinfo($uid)
    {
        Cache::delete(self::userinfoKey($uid));
    }

    /**
     * 清理某个角色下全部管理员的信息缓存
     * 角色权限 / 主题变更后，该角色下管理员的权限与界面需立即生效
     * @param int|string $roleId 角色ID
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function forgetRoleUserinfo($roleId)
    {
        $uids = Admin::where('role_id', $roleId)->column('id');
        foreach ($uids as $uid) {
            self::forgetUserinfo($uid);
        }
    }

    /**
     * 从数据库组装管理员信息
     * @param int $uid 管理员ID
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function buildUserinfo(int $uid): array
    {
        $user = Admin::where('id', $uid)
            ->withoutField([
                'password'
            ])
            ->find();
        if (empty($user)) {
            throw new ExceptionUnauthorized('获取用户信息错误');
        }
        // 用户数据
        $data = $user->toArray();
        if (empty($data['avatar'])) {
            $data['avatar'] = '';
        }
        // 查询管理员角色
        $adminRole = AdminRole::where('id', $data['role_id'])
            ->withoutField([
                'theme'
            ])->find();
        if (empty($adminRole)) {
            throw new ExceptionUnauthorized('获取用户角色错误');
        }
        // 处理角色数据
        $adminRole = $adminRole->toArray();
        // 获取用户权限
        $data['permission'] = self::getRulePaths($adminRole['rule'] ?? []);
        unset($adminRole['rule']);
        // 获取用户角色
        $data['role'] = $adminRole;
        // 附带角色昵称
        $data['role_name'] = $adminRole['title'] ?? '未知权限';
        // 返回用户信息
        return $data;
    }

    /**
     * 获取管理员角色权限规则列表
     * 兼容 {label,value} 对象、重复节点唯一标识后缀等前端提交格式
     * @param array $rules 角色权限规则
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getRulePaths(array $rules)
    {
        $list = [];
        foreach ($rules as $rule) {
            if (is_array($rule)) {
                $rule = $rule['value'] ?? '';
            }
            if (!is_string($rule) || $rule === '') {
                continue;
            }
            // 去掉重复规则节点的唯一标识后缀，还原为 插件标识/菜单地址
            $list[] = preg_replace('/#\d+$/', '', $rule);
        }
        return array_values(array_unique($list));
    }

    /**
     * 验证管理员是否有操作权限
     * @param int $roleId 角色ID
     * @param string $plugin 插件标识
     * @param string $module 模块标识
     * @param string $controller 控制器名称
     * @param string $action 操作方法名称
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function checkAuth(int $roleId, string $plugin, string $module, string $controller, string $action): bool
    {
        if (empty($roleId) || empty($controller) || empty($action)) {
            return false;
        }
        // 通用配置路由由 xbCode 统一注册（/app/{plugin}/admin/Config|Tabs/{name}），
        // 其菜单地址与所属插件需按路由参数还原
        if ($controller === 'Config') {
            $params = request()->route ? (array) request()->route->param() : [];
            if ($action === 'config' && !empty($params['name'])) {
                // admin/{插件}/xxx 形式的配置项
                $action = (string) $params['name'];
                if (!empty($params['plugin'])) {
                    $plugin = (string) $params['plugin'];
                }
            } elseif ($action === 'tabs' && !empty($params['name'])) {
                // admin/Tabs/xxx 形式的选项卡配置
                $controller = 'Tabs';
                $action = (string) $params['name'];
                if (!empty($params['plugin'])) {
                    $plugin = (string) $params['plugin'];
                }
            }
        }
        // 菜单地址（模块/控制器/方法）
        $path = $module ? "{$module}/{$controller}/{$action}" : "{$controller}/{$action}";
        // 检测该地址是否登记为启用状态的权限规则
        $where = [
            ['path', '=', $path],
            ['state', '=', '20'],
        ];
        if (!empty($plugin)) {
            $where[] = ['plugin', '=', $plugin];
        }
        if (!AdminRule::where($where)->count()) {
            // 未登记为权限规则的地址一律拒绝
            return false;
        }
        // 获取角色权限规则
        $rules = AdminRole::where('id', $roleId)->value('rule', '');
        if (is_string($rules)) {
            $rules = json_decode($rules, true);
        }
        $rules = is_array($rules) ? self::getRulePaths($rules) : [];
        // 规则格式为 插件标识/菜单地址，兼容仅存菜单地址的历史数据
        return in_array("app/{$plugin}/{$path}", $rules, true) || in_array($path, $rules, true);
    }
}