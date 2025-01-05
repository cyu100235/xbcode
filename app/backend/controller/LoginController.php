<?php
namespace app\backend\controller;

use Exception;
use support\Request;
use app\model\Admin;
use xbcode\XbController;
use app\model\AdminRule;
use Tinywan\Jwt\JwtToken;
use xbcode\utils\TokenUtil;
use xbcode\utils\PasswdUtil;
use app\validate\AdminValidate;
use xbcode\providers\AppProvider;
use Webman\Captcha\PhraseBuilder;
use Webman\Captcha\CaptchaBuilder;
use xbcode\providers\MenuProvider;
use xbcode\providers\QueueProvider;

/**
 * 登录控制器
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class LoginController extends XbController
{
    /**
     * 服务端无需登录的方法
     * @var array
     */
    protected $server = [
        'login',
        'captcha',
        'user',
        'menus',
    ];

    /**
     * 无需登录的方法
     * @var array
     */
    protected $noLogin = [
        'login',
        'captcha',
    ];

    /**
     * 用户登录
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function login(Request $request)
    {
        // 获取数据
        $post = $request->post();

        // 数据验证
        xbValidate(AdminValidate::class, $post, 'login');

        // 是否开启验证码
        $isVcode = AppProvider::get()['public_api']['captcha'] ?? '';
        if ($isVcode) {
            // 验证码效验
            $captcha = $post['vcode'] ?? '';
            if (empty($captcha)) {
                return $this->fail('请填写验证码');
            }
            $captcha = strtolower($captcha);
            $session = $request->session()->get('captcha');
            if ($captcha !== $session) {
                return $this->fail('验证码错误');
            }
        }

        // 查询数据
        $model = Admin::where('username', $post['username'])->find();
        if (!$model) {
            return $this->fail('登录账号错误');
        }
        // 验证登录密码
        $password  = PasswdUtil::create($post['password']);
        $originPwd = (string) $model['password'];
        if ($password !== $originPwd) {
            return $this->fail('登录密码错误');
        }
        if ($model['state'] == '10') {
            return $this->fail('该用户已被冻结');
        }
        // 更新登录信息
        $ip                = $request->getLocalIp();
        $model->login_ip   = $ip;
        $model->login_time = date('Y-m-d H:i:s');
        $model->save();
        // 生成令牌
        $data = [
            'id' => $model['id'],
            'username' => $model['username'],
            'state' => $model['state'],
            'is_system' => $model['is_system'],
        ];
        $data = TokenUtil::create($data);
        // 返回数据
        return $this->successFul('登录成功', $data);
    }

    /**
     * 渲染验证码
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function captcha()
    {
        // 构造验证码生成器
        $builder = new PhraseBuilder(4, '0123456789');
        // 初始化验证码类
        $builder = new CaptchaBuilder(null, $builder);
        // 设置验证码背景色
        $builder->setBackgroundColor(255, 255, 255);
        // 生成验证码
        $builder->build();
        // 获取验证码的内容
        $captcha = $builder->getPhrase();
        // 将验证码的值转换为小写
        $captcha = strtolower($captcha);
        // 将验证码的值存储到session中
        request()->session()->set('captcha', $captcha);
        // 获得二维码base64内容
        $img_content = $builder->inline();
        // 输出图片内容
        return response($img_content);
    }

    /**
     * 获取用户信息
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function user()
    {
        try {
            $uid = (int) JwtToken::getCurrentId();
            if (empty($uid)) {
                throw new Exception('参数错误，请重新登录', 1200);
            }
            $user = Admin::where('id', $uid)->find();
            if (empty($user)) {
                throw new Exception('用户信息错误，请重新登录', 1200);
            }
            // 用户数据
            $data = $user->toArray();
            if (empty($data['avatar'])) {
                $data['avatar'] = xbUrl('static/image/avatar.png', [], true, true, false);
            }
            return $this->successRes($data);
        } catch (\Throwable $e) {
            return $this->failFul($e->getMessage(), 12000);
        }
    }

    /**
     * 获取用户菜单
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function menus()
    {
        $where = [
            ['plugin', '=', ''],
        ];
        $data  = AdminRule::where($where)->order('sort asc')->select()->toArray();
        $data  = MenuProvider::parseMenu($data, true);
        return $this->successRes($data);
    }
}
