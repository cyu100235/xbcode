<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbAdmin\app\admin\controller;

use support\Request;
use support\Response;
use plugin\xbAdmin\api\Menus;
use plugin\xbAdmin\api\AdminApi;
use Webman\Captcha\PhraseBuilder;
use Webman\Captcha\CaptchaBuilder;
use plugin\xbAdmin\app\model\AdminRole;
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
        xbValidate(AdminValidate::class, $post, 'login');
        $data = AdminApi::username(
            (string) $post['username'],
            (string) $post['password'],
            (string) ($post['captcha'] ?? ''),
        );
        return $this->successRes($data);
    }

    /**
     * 退出登录
     * @param Request $request
     * @return Response
     */
    public function logout(Request $request)
    {
        AdminApi::logout();
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
     * @return Response
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function layouts()
    {
        // 获取默认配置主题
        $config = (array) config('plugin.xbAdmin.theme', []);
        // 当前管理员所属部门配置过主题时，按部门主题覆盖默认布局；
        // 部门只能覆盖 config/theme.php 顶层的可配置项，layouts 各布局尺寸始终以配置文件为准
        $theme = AdminRole::normalizeTheme(
            AdminRole::where('id', (int) ($this->currentUser()['role_id'] ?? 0))->value('theme')
        );
        // 返回主题布局配置
        return $this->successRes(array_merge($config, $theme));
    }
}
