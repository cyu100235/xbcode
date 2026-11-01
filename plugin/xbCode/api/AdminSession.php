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

use support\think\Cache;
use plugin\xbCode\app\model\AdminRole;

/**
 * 后台登录会话
 *
 * 会话登记表统一存放在缓存（按 CACHE_TYPE 走 file / redis），鉴权中间件逐请求校验会话，
 * 不查询数据库；管理员的「登录令牌」列表与踢出登录也基于此表实现。
 *
 * - 会话存在且未过期 → 放行
 * - 会话已被踢出 / 已过期 → 强制重新登录
 * - 所属角色开启单点登录 → 同账号仅保留最新一次登录，其余会话自动失效
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class AdminSession
{
    /**
     * 会话有效期（秒），与登录令牌有效期保持一致
     * @var int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public const TTL = 86400 + 120;

    /**
     * 活跃时间刷新间隔（秒），避免逐请求写缓存
     * @var int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public const TOUCH_INTERVAL = 300;

    /**
     * 会话登记表缓存键名
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected const REGISTRY_KEY = 'admin_sessions';

    /**
     * 角色单点登录配置缓存键名前缀
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected const ROLE_PREFIX = 'admin_role_single_login_';

    /**
     * 校验会话是否仍然有效
     * 单次缓存读取即可完成校验；命中后按间隔节流刷新最后活跃时间
     * @param int $uid 管理员ID
     * @param string $session 会话标识
     * @param bool $singleLogin 所属角色是否开启单点登录
     * @return string 校验通过返回空字符串，否则返回提示文案
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function verify(int $uid, string $session, bool $singleLogin): string
    {
        if ($session === '') {
            return '登录状态已失效，请重新登录';
        }
        $sessions = static::all();
        $info = $sessions[$session] ?? null;
        if (empty($info) || (int) ($info['uid'] ?? 0) !== $uid) {
            return '登录状态已失效，请重新登录';
        }
        if ($singleLogin && !static::isLatestOf($sessions, $uid, $session)) {
            return '该账号已在其他设备登录，您已被强制下线';
        }
        // 刷新最后活跃时间（按间隔节流，避免逐请求写缓存）
        $now = time();
        if ($now - (int) ($info['last_time'] ?? 0) >= static::TOUCH_INTERVAL) {
            $sessions[$session]['last_time'] = $now;
            $sessions[$session]['ip'] = (string) ($info['ip'] ?? '');
            static::save($sessions);
        }
        return '';
    }

    /**
     * 获取全部有效会话（按登录时间倒序）
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function list(): array
    {
        $list = [];
        foreach (static::all() as $session => $info) {
            $info['session'] = (string) $session;
            $list[] = $info;
        }
        usort($list, function ($a, $b) {
            return (int) ($b['login_time'] ?? 0) <=> (int) ($a['login_time'] ?? 0);
        });
        return $list;
    }

    /**
     * 登记登录会话
     * 单点登录角色会先剔除该账号的其它会话，保证同账号仅保留最新一次登录
     * @param array $info 会话信息，必须包含 session / uid
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function register(array $info)
    {
        $session = (string) ($info['session'] ?? '');
        if ($session === '') {
            return;
        }
        $uid = (int) ($info['uid'] ?? 0);
        $sessions = static::all();
        if (!empty($info['single'])) {
            foreach ($sessions as $key => $item) {
                if ((int) ($item['uid'] ?? 0) === $uid) {
                    unset($sessions[$key]);
                }
            }
        }
        $now = time();
        $sessions[$session] = [
            'uid' => $uid,
            'username' => (string) ($info['username'] ?? ''),
            'nickname' => (string) ($info['nickname'] ?? ''),
            'role_id' => (int) ($info['role_id'] ?? 0),
            'role_name' => (string) ($info['role_name'] ?? ''),
            'ip' => (string) ($info['ip'] ?? ''),
            'city' => (string) ($info['city'] ?? ''),
            'user_agent' => (string) ($info['user_agent'] ?? ''),
            'single' => !empty($info['single']) ? '20' : '10',
            'login_time' => (int) ($info['login_time'] ?? $now),
            'last_time' => (int) ($info['last_time'] ?? $now),
            'expire_time' => (int) ($info['expire_time'] ?? $now + static::TTL),
        ];
        static::save($sessions);
    }

    /**
     * 踢出指定会话
     * @param string $session 会话标识
     * @return bool 会话是否真实存在并被移除
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function kick(string $session): bool
    {
        if ($session === '') {
            return false;
        }
        $sessions = static::all();
        if (!isset($sessions[$session])) {
            return false;
        }
        unset($sessions[$session]);
        static::save($sessions);
        return true;
    }

    /**
     * 踢出指定账号的全部会话
     * @param int $uid 管理员ID
     * @return int 被移除的会话数量
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function kickUser(int $uid): int
    {
        $sessions = static::all();
        $count = 0;
        foreach ($sessions as $key => $item) {
            if ((int) ($item['uid'] ?? 0) === $uid) {
                unset($sessions[$key]);
                $count++;
            }
        }
        static::save($sessions);
        return $count;
    }

    /**
     * 踢出全部会话
     * @return int 被移除的会话数量
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function kickAll(): int
    {
        $count = count(static::all());
        static::save([]);
        return $count;
    }

    /**
     * 角色是否开启单点登录
     * @param int $roleId 角色ID
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function roleSingleLogin(int $roleId): bool
    {
        if ($roleId <= 0) {
            return false;
        }
        $key = static::ROLE_PREFIX . $roleId;
        $value = Cache::get($key);
        if ($value === null) {
            $value = (string) AdminRole::where('id', $roleId)->value('is_single_login', '10');
            $value = $value === '20' ? '20' : '10';
            Cache::set($key, $value, static::TTL);
        }
        return $value === '20';
    }

    /**
     * 写入角色的单点登录配置缓存（登录时预热，避免逐请求查库）
     * @param int $roleId 角色ID
     * @param bool $single 是否开启单点登录
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function setRoleSingleLogin(int $roleId, bool $single)
    {
        if ($roleId <= 0) {
            return;
        }
        Cache::set(static::ROLE_PREFIX . $roleId, $single ? '20' : '10', static::TTL);
    }

    /**
     * 清理角色的单点登录配置缓存（角色变更时调用）
     * @param int $roleId 角色ID
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function forgetRole(int $roleId)
    {
        Cache::delete(static::ROLE_PREFIX . $roleId);
    }

    /**
     * 判断是否为该账号最新一次登录的会话
     * @param array $sessions 会话登记表
     * @param int $uid 管理员ID
     * @param string $session 会话标识
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function isLatestOf(array $sessions, int $uid, string $session): bool
    {
        $latest = '';
        $latestTime = -1;
        foreach ($sessions as $key => $item) {
            if ((int) ($item['uid'] ?? 0) !== $uid) {
                continue;
            }
            $time = (int) ($item['login_time'] ?? 0);
            if ($time >= $latestTime) {
                $latestTime = $time;
                $latest = (string) $key;
            }
        }
        return $latest !== '' && $latest === $session;
    }

    /**
     * 读取会话登记表（自动剔除已过期会话）
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function all(): array
    {
        $data = Cache::get(static::REGISTRY_KEY);
        if (!is_array($data)) {
            return [];
        }
        $now = time();
        foreach ($data as $key => $item) {
            if ((int) ($item['expire_time'] ?? 0) <= $now) {
                unset($data[$key]);
            }
        }
        return $data;
    }

    /**
     * 写入会话登记表
     * @param array $sessions 会话列表
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function save(array $sessions)
    {
        Cache::set(static::REGISTRY_KEY, $sessions, static::TTL);
    }
}
