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
use plugin\xbCode\api\AdminApi;
use Webman\Captcha\PhraseBuilder;
use plugin\xbCode\app\model\Admin;
use Webman\Captcha\CaptchaBuilder;
use plugin\xbCode\app\validate\AdminValidate;
use plugin\xbCode\exception\business\ExceptionUnauthorized;

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
     * 用户登录
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function login()
    {
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
    }

    /**
     * 获取图像验证码
     * @return bool|string
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
        // 输出图片内容
        return $imgContent;
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
        if (empty($uid)) {
            throw new ExceptionUnauthorized('当前尚未登录，请先登录');
        }
        $user = Admin::where('id', $uid)->find();
        if (empty($user)) {
            throw new ExceptionUnauthorized('获取用户信息错误');
        }
        // 用户数据
        $data = $user->toArray();
        if (empty($data['avatar'])) {
            $data['avatar'] = '';
        }
        return $this->successRes($data);
    }

    /**
     * 获取管理员菜单
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function menus()
    {
        $uid = (int) request()->uid;
        $data = Menus::get($uid);
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
            'footerHeight' => 35,
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
