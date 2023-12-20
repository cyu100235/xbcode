<?php
namespace app\admin\controller;

use app\common\validate\LoginValidate;
use app\common\BaseController;
use app\common\model\Admin;
use app\common\utils\AuthUtil;
use app\common\utils\MenusUtil;
use app\common\utils\PasswordUtil;
use Exception;
use think\Request;

class LoginController extends BaseController
{
    /**
     * 无需登录方法
     * @var array
     */
    protected $noLogin = ['login'];

    /**
     * 用户登录
     * @param \think\Request $request
     * @throws \Exception
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function login(Request $request)
    {
        # 获取数据
        $post = $request->post();

        # 数据验证
        xbValidate(LoginValidate::class, $post);

        // 查询数据
        $where      = [
            'username' => $post['username']
        ];
        $model = Admin::with(['role'])->where($where)->find();
        if (!$model) {
            throw new Exception('登录账号错误');
        }
        // 验证登录密码
        if (!PasswordUtil::passwordVerify((string) $post['password'], (string) $model['password'])) {
            throw new Exception('登录密码错误');
        }
        if ($model['status'] == '10') {
            throw new Exception('该用户已被冻结');
        }        
        // 更新登录信息
        $ip                 = request()->ip();
        $model->login_ip    = $ip;
        $model->login_time  = date('Y-m-d H:i:s');
        $model->save();

        $user  = $model->toArray();
        $token = AuthUtil::encrypt($user);
        # 跳转地址
        $url = 'Index/index';
        if (!empty($user['project']['name'])) {
            $url = "/xbase/{$user['project']['name']}/admin/";
        }
        # 返回数据
        $data = [
            'token'     => $token,
            'url'       => $url
        ];
        return $this->successFul('登录成功',$data);
    }

    /**
     * 获取用户信息
     * @param \think\facade\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function user(Request $request)
    {
        $userId = $request->userId;
        if (empty($userId)) {
            return $this->failFul('参数错误，请重新登录',12000);
        }
        $where = [
            'id'    => $userId
        ];
        $user = Admin::with(['role'])->where($where)->find();
        if (empty($user)) {
            return $this->failFul('用户信息错误，请重新登录',12000);
        }
        # 前端数据
        $data = $user->toArray();
        $data['menus'] = MenusUtil::getMenus();
        return $this->successRes($data);
    }

    /**
     * 退出登录
     * @param \think\facade\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function exit(Request $request)
    {
        return $this->successFul('登录成功',[]);
    }
}
