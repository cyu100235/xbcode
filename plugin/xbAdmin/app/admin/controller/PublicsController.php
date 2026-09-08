<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\app\admin\controller;

use Throwable;
use support\Request;
use support\Response;
use plugin\xbAdmin\api\Menus;
use plugin\xbAdmin\api\AdminApi;
use Webman\Captcha\PhraseBuilder;
use plugin\xbAdmin\app\model\AdminRole;
use Webman\Captcha\CaptchaBuilder;
use plugin\xbAdmin\app\validate\AdminValidate;
use plugin\xbAdmin\exception\business\ExceptionBusiness;
use plugin\xbAdmin\exception\business\ExceptionUnauthorized;

/**
 * 登录与公共接口控制器
 *
 * 该控制器承载 SPA 启动后的全部公共读接口：
 * 登录、退出、验证码、当前用户、菜单、布局配置。
 */
class PublicsController extends BaseController
{
    /**
     * 免登录方法
     * @var array
     */
    protected $noLogin = [
        'login',
        'captcha',
    ];

    /**
     * 免权限校验方法，登录后即可访问
     * @var array
     */
    protected $noAuth = [
        'logout',
        'user',
        'menus',
        'layouts',
        'refresh',
    ];

    /**
     * 账号登录
     * @param Request $request
     * @throws ExceptionBusiness
     * @return Response
     */
    public function login(Request $request)
    {
        if ($request->method() !== 'POST') {
            throw new ExceptionBusiness('登录方式错误，请使用POST提交');
        }
        $post = $request->post();
        xbAdminValidate(AdminValidate::class, (array) $post, 'login');
        $captcha = $post['vcode'] ?? $post['captcha'] ?? $post['code'] ?? '';
        $data = AdminApi::username(
            (string) $post['username'],
            (string) $post['password'],
            $captcha === null ? null : (string) $captcha
        );
        return $this->successRes($data, '登录成功');
    }

    /**
     * 退出登录
     * @param Request $request
     * @return Response
     */
    public function logout(Request $request)
    {
        try {
            AdminApi::logout();
        } catch (Throwable $e) {
            // 令牌已过期或已被注销时不影响前端退出流程
        }
        return $this->success('已退出登录');
    }

    /**
     * 刷新登录令牌
     * @param Request $request
     * @return Response
     */
    public function refresh(Request $request)
    {
        return $this->successRes(AdminApi::refreshToken());
    }

    /**
     * 获取图形验证码
     *
     * 验证码文本保存在当前会话中，图片以 image/jpeg 直接输出，
     * 前端通过 <img :src="public_api.captcha"> 展示。
     * @param Request $request
     * @throws ExceptionBusiness
     * @return Response
     */
    public function captcha(Request $request)
    {
        if (!AdminApi::captchaState()) {
            throw new ExceptionBusiness('系统未开启验证码');
        }
        $builder = new CaptchaBuilder(null, new PhraseBuilder(4, '0123456789'));
        $builder->setBackgroundColor(255, 255, 255);
        $builder->build();
        $phrase = strtolower((string) $builder->getPhrase());
        $request->session()->set(AdminApi::CAPTCHA_KEY, $phrase);
        $content = (string) $builder->get();
        return response($content)
            ->withHeader('Content-Type', 'image/jpeg')
            ->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    /**
     * 获取当前登录管理员信息
     * @param Request $request
     * @throws ExceptionUnauthorized
     * @return Response
     */
    public function user(Request $request)
    {
        $model = $this->currentModel();
        $data = $model->toArray();
        $data['avatar'] = (string) ($data['avatar'] ?? '');
        $data['role_title'] = (string) AdminRole::where('id', (int) $model['role_id'])->value('title');
        return $this->successRes($data);
    }

    /**
     * 获取当前登录管理员菜单
     * @param Request $request
     * @return Response
     */
    public function menus(Request $request)
    {
        $data = Menus::get($this->adminId());
        return $this->successRes($data);
    }

    /**
     * 获取布局配置
     *
     * 前端路由守卫拿到假值会直接中断跳转并提示「获取主题配置失败」，
     * 因此这里必须保证任何情况下都返回完整的布局对象。
     * @param Request $request
     * @return Response
     */
    public function layouts(Request $request)
    {
        $default = [
            // 布局模式 default默认布局 sideBar侧边双栏 user用户中心
            'layoutMode' => 'sideBar',
            // 主题类型 light浅色 dark深色 OS跟随系统
            'theme' => 'OS',
            // 主题自定义样式
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
            'sideMenuOrdinaryWidth' => 200,
            // 侧边栏折叠时宽度
            'sideMenuCollapseWidth' => 64,
        ];
        $config = (array) xbAdminConfig('xbadmin.layout', []);
        $data = array_merge($default, $config);
        foreach ($default as $key => $value) {
            if ($data[$key] === '' || $data[$key] === null) {
                $data[$key] = $value;
            }
        }
        return $this->successRes($data);
    }
}
