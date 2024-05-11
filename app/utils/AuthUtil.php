<?php
namespace app\utils;

use app\providers\MenuProvider;
use Tinywan\Jwt\JwtToken;
use app\model\Admin;
use Exception;

/**
 * 权限工具类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class AuthUtil
{
    /**
     * 检测是否拥有权限
     * @param int $uid
     * @param string $path
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function canAuth(int $uid, string $path)
    {
    }

    /**
     * 生成token
     * @param int $uid
     * @param string $state
     * @param mixed $expire
     * @param string $client
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function generateToken(array $data, $expire = 604800, string $client = 'WEB')
    {
        if (!isset($data['id'])) {
            throw new Exception('参数错误，缺少ID');
        }
        if (!isset($data['state'])) {
            throw new Exception('参数错误，缺少状态');
        }
        // 构建数据
        $data  = [
            'id' => $data['id'],
            'state' => $data['state'],
            'client' => $client,
            'access_exp' => $expire,
        ];
        $token = JwtToken::generateToken($data);
        return $token;
    }

    /**
     * 刷新token
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function refreshToken()
    {
    }

    // TODO：未完成获取用户专属角色规则
    /**
     * 获取用户权限
     * @param int $uid
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getAuthRule(int $uid)
    {
        $model = Admin::with(['rule'])->where('id', $uid)->find();
        if (!$model) {
            throw new Exception('用户信息错误，请重新登录', 12000);
        }
        // 检测是否系统管理员
        if ($model['is_system'] == '20') {
            // 获取菜单数据
            $menus = MenuProvider::menuList();
            // 解析数据格式
            $data = MenuProvider::parseData($menus);
            // 返回数据
            return $data;
        }
        $rule = $model['rule'];
        if (empty($rule)) {
            return [];
        }
        return [];
    }
}
