<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\app\admin\controller;

use Exception;
use support\think\Cache;
use plugin\xbCode\api\Menus;
use plugin\xbCode\api\Layouts;
use plugin\xbCode\api\AdminApi;
use Webman\Captcha\PhraseBuilder;
use Webman\Captcha\CaptchaBuilder;
use plugin\xbCode\app\validate\AdminValidate;
use plugin\xbCode\exception\business\ExceptionInternalServerError;

/**
 * 登录控制器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class PublicsController extends BaseController
{
    /**
     * 无需登录的方法
     * @var array
     */
    protected $noLogin = [
        'login',
        'captcha',
    ];

    /**
     * 无需权限校验的方法
     * 均为已登录即可访问的后台基础数据接口，不涉及业务操作
     * @var array
     */
    protected $noAuth = [
        'menus',
        'layouts',
    ];

    /**
     * 用户登录
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function login()
    {
        try {
            // 获取数据
            $post = request()->post();
            // 数据验证
            xbValidate(AdminValidate::class, $post, 'login');
            // 账号登录
            $data = AdminApi::username(
                $post['username'],
                $post['password'],
                $post['vcode'] ?? ''
            );
            // 返回数据
            return $this->successRes($data);
        } catch (\Throwable $th) {
            throw new ExceptionInternalServerError($th->getMessage());
        }
    }

    /**
     * 获取图像验证码
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
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
        // 将验证码的值存储到缓存中
        $session = request()->session()->getId();
        Cache::set($session, $captcha, 300);
        // 获得二维码base64内容
        $imgContent = $builder->get();
        // 输出图片内容：禁止浏览器缓存，避免复用旧图片导致验证码与缓存不一致
        return response($imgContent, 200, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * 获取用户信息
     * @throws Exception
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function user()
    {
        $uid = (int) request()->uid;
        $data = AdminApi::userinfo($uid);
        return $this->successRes($data);
    }

    /**
     * 获取所有菜单
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function menus()
    {
        $data = Menus::get();
        return $this->successRes($data);
    }

    /**
     * 获取布局视图
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function layouts()
    {
        // 获取当前登录管理员的角色主题
        $data = Layouts::role((int) request()->role_id);
        return $this->successRes($data);
    }
}
