<?php
namespace xbcode\service\xbcode;

use Exception;
use support\Cache;

/**
 * 用户服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class UserService extends XbBaseService
{
    /**
     * 用户登录
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function login(string $username,string $password)
    {
        if (empty($username)) {
            throw new Exception('请填写用户名');
        }
        if (empty($password)) {
            throw new Exception('请填写登录密码');
        }
        // 项目标识
        $name = config('projects.name');
        // 项目版本
        $version = config('projects.version');
        // 服务器IP地址
        $serviceIp = file_get_contents('http://ifconfig.me/ip');
        // 当前域名
        $domain = request()->host();
        // 请求数据
        $data   = [
            'username' => $username,
            'password' => $password,
            'project_name' => $name,
            'project_version' => $version,
            'domain' => $domain,
            'server_ip' => $serviceIp,
        ];
        $server = static::request()->post('User/login', $data);
        $result = $server->array();
        if (empty($result)) {
            throw new Exception('网络请求错误');
        }
        if (isset($result['code']) && $result['code'] != 200) {
            throw new Exception($result['msg']);
        }
        if (empty($result['data']['access_token'])) {
            throw new Exception('登录失败');
        }
        // 保存登录信息
        Cache::set('xb_server_token', $result['data']['access_token']);
        // 返回数据
        return $result;
    }
    
    /**
     * 用户注册
     * @param string $username
     * @param string $password
     * @param string $nickname
     * @param string $vcode
     * @return array|mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function register(string $username,string $password, string $nickname, string $vcode)
    {
        $data   = [
            'username' => $username,
            'password' => $password,
            'nickname' => $nickname,
            'vcode' => $vcode,
        ];
        $result = static::request()->post('User/register')->array();
        if (!isset($result['code']) && $result['code'] != 200) {
            throw new Exception('网络请求错误');
        }
        return $result;
    }
    
    /**
     * 修改密码
     * @param string $username 手机号码
     * @param string $password 新登录密码
     * @param string $vcode 验证码
     * @return array|mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function password(string $username,string $password,string $vcode)
    {
        $data   = [
            'username' => $username,
            'password' => $password,
            'vcode'    => $vcode,
        ];
        $result = static::request()->put('User/password',$data)->array();
        if (!isset($result['code']) && $result['code'] != 200) {
            throw new Exception('网络请求错误');
        }
        return $result;
    }
}