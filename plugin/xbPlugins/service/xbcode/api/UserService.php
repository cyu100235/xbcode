<?php
namespace plugin\xbPlugins\service\xbcode\api;

use Exception;
use plugin\xbPlugins\service\xbcode\BaseServer;
use plugin\xbPlugins\service\xbcode\XbCodeServer;

/**
 * 用户服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class UserService extends XbCodeServer
{
    /**
     * 获取登录二维码
     * @throws \Exception
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function loginQrcode()
    {
        $service = static::request()->get('xbUser/api/Publics/qrcode');
        $result = $service->array();
        if (empty($result)) {
            throw new Exception('网络请求错误');
        }
        if (isset($result['code']) && $result['code'] != 200) {
            throw new Exception($result['msg'], $result['code']);
        }
        if (empty($result['data'])) {
            throw new Exception('获取登录二维码失败');
        }
        return $result['data'];
    }
    
    /**
     * 检查登录状态
     * @param string $code
     * @throws \Exception
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function checkeLogin(string $code)
    {
        $service = static::request()->get('xbUser/api/Publics/checked', [
            'code' => $code
        ]);
        $result = $service->array();
        if (empty($result)) {
            throw new Exception('网络请求错误');
        }
        if (isset($result['code']) && $result['code'] != 200) {
            throw new Exception($result['msg'], $result['code']);
        }
        if (empty($result['data'])) {
            throw new Exception('检查登录状态失败');
        }
        if (empty($result['data']['token']['access_token'])) {
            throw new Exception('登录失败，缺失令牌参数');
        }
        if (empty($result['data']['user']['id'])) {
            throw new Exception('登录失败，缺失用户ID参数');
        }
        // 缓存TOKEN
        BaseServer::setToken($result['data']['token']['access_token']);
        // 设置用户信息
        static::setUser($result['data']['user']);
        // 返回用户信息
        return $result['data']['user'];
    }
    
    /**
     * 获取用户信息
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function userinfo()
    {
        $key = static::getUserKey();
        $user = request()->session()->get($key);
        return $user;
    }

    /**
     * 用户退出
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function logout()
    {
        $key = static::getUserKey();
        request()->session()->delete($key);
        $key = BaseServer::getServiceTokenName();
        request()->session()->delete($key);
    }

    /**
     * 设置用户信息
     * @param array $user
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function setUser(array $user)
    {
        $key = static::getUserKey();
        request()->session()->set($key, $user);
    }

    /**
     * 获取用户KEY
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private static function getUserKey()
    {
        $key = BaseServer::getServiceTokenName();
        $key = $key . '_user';
        return $key;
    }
}