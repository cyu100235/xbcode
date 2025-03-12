<?php
namespace plugin\xbCode\app\admin\controller;

use Exception;
use support\Request;
use plugin\xbCode\XbController;
use plugin\xbCode\api\AdminMenus;
use Webman\Captcha\PhraseBuilder;
use plugin\xbCode\app\model\Admin;
use plugin\xbCode\utils\TokenUtil;
use Webman\Captcha\CaptchaBuilder;
use plugin\xbCode\api\AppsEntry;
use plugin\xbCode\utils\PasswdUtil;
use plugin\xbCode\app\validate\AdminValidate;

/**
 * 登录控制器
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PublicsController extends XbController
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
        $isVcode = AppsEntry::get()['public_api']['captcha'] ?? '';
        if ($isVcode && class_exists('Webman\Captcha\CaptchaBuilder')) {
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
        $data = $model->toArray();
        unset($data['password']);
        $result = TokenUtil::create($data);
        // 设置登录数据
        $request->session()->set('xbcode', $data);
        // 日志数据
        $request->uid = $model['id'];
        $request->username = $model['username'];
        $request->saasAppid = $model['saas_appid'];
        // 返回数据
        return $this->successFul('登录成功', $result);
    }
    
    /**
     * 获取图像验证码
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function captcha(Request $request)
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
        $request->session()->set('captcha', $captcha);
        // 获得二维码base64内容
        $img_content = $builder->inline();
        // 输出图片内容
        return response($img_content);
    }
    
    /**
     * 获取用户信息
     * @param \support\Request $request
     * @throws \Exception
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function user(Request $request)
    {
        try {
            $uid = (int) $request->uid;
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
     * 获取管理员菜单
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function menus(Request $request)
    {
        $uid  = (int)$request->uid;
        $data  = AdminMenus::get($uid);
        return $this->successRes($data);
    }
}
