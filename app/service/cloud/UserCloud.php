<?php
namespace app\service\cloud;
use app\utils\JsonUtil;
use support\Request;

/**
 * 用户云服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait UserCloud
{
    use JsonUtil;

    /**
     * 用户登录
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function login(Request $request)
    {
        $username = $request->post('username');
        $password = $request->post('password');
        $params = [
            'username'  => $username,
            'password'  => $password,
        ];
        $data = HttpCloud::post('user/Login/login',$params);
        // 验证数据
        $data = HttpCloud::getContent($data);
        // 储存token
        HttpCloud::setToken($data);
        // 返回数据
        return self::successFul('云服务登录成功',$data);
    }

    /**
     * 获取用户信息
     * @param \support\Request $request
     * @return array|mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function userInfo(Request $request)
    {
        $data = HttpCloud::get('user/Home/index')->array();
        if (empty($data)) {
            return self::fail('获取用户信息失败');
        }
        if (isset($data['code']) && $data['code'] === 12000) {
            return self::failFul($data['data'],11000);
        }
        if (isset($data['code']) && $data['code'] != 200) {
            return self::fail($data['msg'], $data['code']);
        }
        // 返回数据
        return self::successRes($data['data'] ?? []);
    }
}