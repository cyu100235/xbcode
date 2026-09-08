<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\api;

use JsonSerializable;

/**
 * 站点配置接口
 *
 * 输出积木云 SPA 启动时必需的 site 数据结构，
 * 全部数据来源于 plugin/xbAdmin/config/xbadmin.php，不依赖其他插件。
 *
 * 前端强校验的键（缺失会直接白屏）：
 * public_api.login / public_api.user / public_api.menus / public_api.layouts / public_view.workbench
 */
class SiteEntry implements JsonSerializable
{
    /**
     * 插件标识
     * @var string
     */
    protected string $plugin = '';

    /**
     * 模块标识
     * @var string
     */
    protected string $module = 'admin';

    /**
     * 插件配置
     * @var array
     */
    protected array $config = [];

    /**
     * 创建实例
     * @param string $module 模块标识
     * @param string $plugin 插件标识，留空自动获取
     * @return static
     */
    public static function make(string $module = 'admin', string $plugin = ''): static
    {
        $instance = new static;
        $instance->module = $module !== '' ? $module : 'admin';
        if ($plugin === '') {
            $plugin = (string) (request()->plugin ?? '');
        }
        $instance->plugin = $plugin !== '' ? $plugin : 'xbAdmin';
        $instance->config = (array) xbAdminConfig('xbadmin', []);
        return $instance;
    }

    /**
     * 获取站点配置
     * @return array
     */
    public function get(): array
    {
        $config = $this->config;
        $captchaState = (string) ($config['captcha_state'] ?? '10');
        $data = [
            'web_name' => (string) ($config['web_name'] ?? '后台管理系统'),
            'web_logo' => (string) ($config['web_logo'] ?? ''),
            'web_version' => (string) ($config['web_version'] ?? '1.0.0'),
            'web_url' => (string) ($config['web_url'] ?? ''),
            'web_icp' => (string) ($config['web_icp'] ?? ''),
            'web_police' => (string) ($config['web_police'] ?? ''),
            'about_name' => (string) ($config['about_name'] ?? ''),
            'about_url' => (string) ($config['about_url'] ?? ''),
            'captcha_state' => $captchaState,
        ];
        $data['copyright'] = $this->copyright((string) ($config['copyright'] ?? ''));
        $data['login_data'] = $this->loginData((array) ($config['login'] ?? []));
        $data['public_api'] = $this->publicApi((array) ($config['public_api'] ?? []), $captchaState);
        $data['public_view'] = $this->publicView((array) ($config['public_view'] ?? []));
        $data['upload_api'] = $this->fillApi((array) ($config['upload_api'] ?? []), ['index', 'upload', 'chunk', 'edit', 'move', 'del']);
        $data['upload_cate_api'] = $this->fillApi((array) ($config['upload_cate_api'] ?? []), ['index', 'add', 'edit', 'del']);
        $data['editor_upload_api'] = $this->fillApi((array) ($config['editor_upload_api'] ?? []), ['image', 'video', 'file']);
        $data['components'] = (array) ($config['components'] ?? []);
        $data['global_components'] = (array) ($config['global_components'] ?? []);
        $data['icons_links'] = (array) ($config['icons_links'] ?? []);
        return $data;
    }

    /**
     * JSON序列化
     * @return array
     */
    public function jsonSerialize(): mixed
    {
        return $this->get();
    }

    /**
     * 生成后台接口地址
     * @param string $path 控制器/方法 或 方法名
     * @return string
     */
    public function url(string $path): string
    {
        return Url::make($path)
            ->plugin($this->plugin)
            ->module($this->module)
            ->get();
    }

    /**
     * 登录页数据
     * @param array $config
     * @return array
     */
    protected function loginData(array $config): array
    {
        $loginTitle = (string) ($config['login_title'] ?? '');
        $loginDesc = (string) ($config['login_desc'] ?? '');
        // 前端读取 login_bg / login_ad，同时保留 bg / ad 旧键位
        $loginBg = (string) ($config['login_bg'] ?? $config['bg'] ?? '');
        $loginAd = (string) ($config['login_ad'] ?? $config['ad'] ?? '');
        return [
            'login_title' => $loginTitle,
            'login_desc' => $loginDesc,
            'login_bg' => $loginBg,
            'login_ad' => $loginAd,
            'bg' => $loginBg,
            'ad' => $loginAd,
            'register' => (string) ($config['register'] ?? ''),
            'findpwd' => (string) ($config['findpwd'] ?? ''),
            'back_url' => (string) ($config['back_url'] ?? ''),
            'other_login' => (array) ($config['other_login'] ?? []),
        ];
    }

    /**
     * 公共接口地址
     * @param array $config
     * @param string $captchaState
     * @return array
     */
    protected function publicApi(array $config, string $captchaState): array
    {
        // 仅在开启验证码时下发验证码地址，前端同时校验两个条件
        $captcha = $config['captcha'] ?? '';
        if ($captcha === '') {
            $captcha = $captchaState === '20' ? $this->url('Publics/captcha') : '';
        }
        $data = [
            'captcha' => (string) $captcha,
            'login' => (string) ($config['login'] ?? $this->url('Publics/login')),
            'loginout' => (string) ($config['loginout'] ?? $this->url('Publics/logout')),
            'user' => (string) ($config['user'] ?? $this->url('Publics/user')),
            'menus' => (string) ($config['menus'] ?? $this->url('Publics/menus')),
            'layouts' => (string) ($config['layouts'] ?? $this->url('Publics/layouts')),
        ];
        return array_merge($data, $config);
    }

    /**
     * 公共视图地址
     * @param array $config
     * @return array
     */
    protected function publicView(array $config): array
    {
        $data = [
            // 自定义登录页视图，留空使用内置登录页
            'login' => (string) ($config['login'] ?? ''),
            // 个人资料视图
            'user' => (string) ($config['user'] ?? $this->url('Admin/profile')),
            // 顶部工具栏视图
            'toolbar' => (string) ($config['toolbar'] ?? $this->url('Index/toolbar')),
            // 工作台视图
            'workbench' => (string) ($config['workbench'] ?? $this->url('Index/workbench')),
        ];
        return array_merge($data, $config);
    }

    /**
     * 补全接口键位，未配置的键置空
     * @param array $config
     * @param array $fields
     * @return array
     */
    protected function fillApi(array $config, array $fields): array
    {
        $data = [];
        foreach ($fields as $field) {
            $data[$field] = (string) ($config[$field] ?? '');
        }
        return array_merge($data, $config);
    }

    /**
     * 解析版权信息中的占位符
     * @param string $copyright
     * @return string
     */
    protected function copyright(string $copyright): string
    {
        if ($copyright === '') {
            return '';
        }
        $variables = ['{WEB_NAME}', '{WEB_URL}', '{WEB_ICP}', '{WEB_POLICE}'];
        $values = [
            (string) ($this->config['web_name'] ?? ''),
            (string) ($this->config['web_url'] ?? ''),
            (string) ($this->config['web_icp'] ?? ''),
            (string) ($this->config['web_police'] ?? ''),
        ];
        return str_replace($variables, $values, $copyright);
    }
}
