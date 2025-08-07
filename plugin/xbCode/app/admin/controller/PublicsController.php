<?php
namespace plugin\xbCode\app\admin\controller;

use Exception;
use support\Request;
use plugin\xbCode\api\Menus;
use plugin\xbCode\api\AdminApi;
use plugin\xbCode\XbController;
use Webman\Captcha\PhraseBuilder;
use plugin\xbCode\app\model\Admin;
use Webman\Captcha\CaptchaBuilder;
use plugin\xbCode\app\validate\AdminValidate;

/**
 * 登录控制器
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PublicsController extends XbController
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
        // 账号登录
        $data = AdminApi::accountLogin($post['username'], $post['password'], $post['vcode'] ?? '');
        // 返回数据
        return $this->successRes($data);
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
                throw new Exception('参数错误，请重新登录', 12000);
            }
            $user = Admin::where('id', $uid)->find();
            if (empty($user)) {
                throw new Exception('用户信息错误，请重新登录', 12000);
            }
            // 用户数据
            $data = $user->toArray();
            if (empty($data['avatar'])) {
                $data['avatar'] = xbUrl('static/image/avatar.png', [], [
                    'slash' => true,
                    'domain' => true,
                    'module' => false,
                ]);
            }
            return $this->successRes($data);
        } catch (\Throwable $e) {
            return $this->kickout($e->getMessage());
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
        $uid = (int) $request->uid;
        $data = Menus::get($uid);
        return $this->successRes($data);
    }

    /**
     * 获取布局视图
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function layouts(Request $request)
    {
        $data = [
            // 布局模式 default默认布局 sideBar侧边双栏 user用户中心
            'layoutMode' => 'sideBar',
            // 主题类型 light浅色 dark深色 OS跟随系统
            'theme' => 'OS',
            // 主题样式
            'themeCss' => '',
            // 是否折叠菜单
            'isCollapse' => false,
            // 底部高度
            'footerHeight' => 40,
            // 头部高度
            'headerHeight' => 60,
            // 图标大小
            'logoSize' => 40,
            // 侧边栏未折叠宽度
            'sideMenuOrdinaryWidth' => 250,
            // 侧边栏折叠时宽度
            'sideMenuCollapseWidth' => 80,
        ];
        return $this->successRes($data);
    }
}
