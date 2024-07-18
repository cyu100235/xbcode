<?php
namespace app\admin\controller;

use app\admin\validate\LoginValidate;
use app\model\Admin;
use app\common\utils\AuthUtil;
use app\common\utils\PasswordUtil;
use hg\apidoc\annotation as Apidoc;
use app\common\XbController;
use support\Request;
use Exception;
use Tinywan\Jwt\JwtToken;

/**
 * 管理员登录
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class LoginController extends XbController
{
    /**
     * 无需登录方法
     * @var array
     */
    protected $noLogin = [
        'login'
    ];

    /**
     * 用户登录
     * @Apidoc\Method("POST")
     * @Apidoc\Param("username", type="string", require=true, desc="登录账户")
     * @code \hg\apidoc\annotation\Param("password", type="string", require=true, desc="登录密码")
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function login(Request $request)
    {
        // 获取数据
        $post = $request->post();

        // 数据验证
        xbValidate(LoginValidate::class, $post);

        // 查询数据
        $model = Admin::with(['role'])->where('username', $post['username'])->find();
        if (!$model) {
            throw new Exception('登录账号错误');
        }
        // 验证登录密码
        if (!PasswordUtil::passwordVerify((string) $post['password'], (string) $model['password'])) {
            throw new Exception('登录密码错误');
        }
        if ($model['state'] == '10') {
            throw new Exception('该用户已被冻结');
        }
        // 更新登录信息
        $ip                = $request->getRealIp();
        $model->login_ip   = $ip;
        $model->login_time = date('Y-m-d H:i:s');
        $model->save();
        // 生成令牌
        $data        = [
            'id' => $model['id'],
            'state' => $model['state'],
        ];
        $data        = AuthUtil::generateToken($data);
        $data['url'] = 'Index/index';
        // 返回数据
        return $this->successFul('登录成功', $data);
    }

    /**
     * 获取用户信息
     * @Apidoc\Method ("GET")
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function user(Request $request)
    {
        try {
            $userId = (int) JwtToken::getCurrentId();
            if (empty($userId)) {
                throw new Exception('参数错误，请重新登录');
            }
            $user = Admin::with(['role'])->where('id', $userId)->find();
            if (empty($user)) {
                throw new Exception('用户信息错误，请重新登录');
            }
            // 前端数据
            $data          = $user->toArray();
            $data['menus'] = AuthUtil::getAuthRule($userId);
            return $this->successRes($data);
        } catch (\Throwable $e) {
            return $this->failFul($e->getMessage(), 12000);
        }
    }

    /**
     * 退出登录
     * @Apidoc\Method ("GET")
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function exit(Request $request)
    {
        return $this->successFul('登录成功', []);
    }
}
