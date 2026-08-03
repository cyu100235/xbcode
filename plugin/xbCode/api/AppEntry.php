<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\api;

use Exception;
use JsonSerializable;

/**
 * 应用接口类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class AppEntry implements JsonSerializable
{
    /**
     * 模块名称
     * @var string
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    protected string $module = '';

    /**
     * 系统信息
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $system = [];

    /**
     * 组织及备案信息
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $webIcp = [];

    /**
     * 登录页数据
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $loginData = [];

    /**
     * 其他登录方式
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $otherLogin = [];

    /**
     * 公用接口
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $publicApi = [];

    /**
     * 公用视图
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $publicView = [];

    /**
     * 上传分类接口
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $uploadCateApi = [];

    /**
     * 上传接口
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $uploadApi = [];

    /**
     * 编辑器上传接口
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $editorUploadApi = [];

    /**
     * 组件接口
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $components = [];

    /**
     * 全局组件
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $global_components = [];

    /**
     * 扩展图标库
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $icons_links = [];

    /**
     * 实例化
     * @param string $module 模块化
     * @return AppEntry
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    public static function make(string $module)
    {
        $instance = new static;
        $instance->module = $module;
        $instance->system();
        $instance->webIcp();
        $instance->otherLogins();
        $instance->loginData();
        $instance->publicApi();
        $instance->publicView();
        $instance->uploadCateApi();
        $instance->uploadApi();
        $instance->editorUploadApi();
        $instance->components();
        return $instance;
    }

    /**
     * 获取系统信息
     * @param array $config
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function system(array $config = [])
    {
        $systemConfig = ConfigApi::make('system')->get('system', []);
        $config = array_merge($systemConfig, $config);
        $this->system = $config;
        return $this;
    }

    /**
     * 组织及备案信息
     * @param array $config
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function webIcp(array $config = [])
    {
        if (empty($config)) {
            $config = ConfigApi::make('webicp')->get('', []);
        }
        if (isset($config['copyright']) && $config['copyright']) {
            /**
             * 变量占位符替换
             * {ABOUT_NAME} 组织名称
             * {ABOUT_URL} 组织链接
             * {WEB_ICP} 备案号码
             * {WEB_POLICE} 公安备案号码
             * {WEB_POLICE_CODE} 公安备案编号
             */
            $variables = [
                '{WEB_NAME}',
                '{WEB_URL}',
                '{ABOUT_NAME}',
                '{ABOUT_URL}',
                '{WEB_ICP}',
                '{WEB_POLICE}',
                '{WEB_POLICE_CODE}',
            ];
            $values = [
                $this->system['web_name'] ?? '',
                $this->system['web_url'] ?? '',
                $config['about_name'] ?? '',
                $config['about_url'] ?? '',
                $config['web_icp'] ?? '',
                $config['web_police'] ?? '',
                $config['web_police_code'] ?? '',
            ];
            $config['copyright'] = str_replace($variables, $values, $config['copyright']);
        }
        $this->webIcp = $config;
        return $this;
    }

    /**
     * 其他登录方式
     * 示例：
     * [
     *     [
     *         'title' => '微信登录',
     *         'name' => 'wechat',
     *         'icon' => 'wechat',
     *         'url' => 'https://www.xbcode.net/wechat/login',
     *     ],
     * ]
     * @param array $config
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function otherLogins(array $config = [])
    {
        $this->loginData['other_login'] = $config ?: [];
        return $this;
    }

    /**
     * 其他登录方式可
     * - `title` 登录名称
     * - `name` 登录标识
     * - `icon` 登录图标
     * - `url` 页面地址
     * @param array $config
     * @return static
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    public function otherLogin(array $config)
    {
        $this->loginData['other_login'][] = $config ?: [];
        return $this;
    }

    /**
     * 登录页数据
     * @param array $config
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function loginData(array $config = [])
    {
        $this->loginData = [
            // 登录标题
            'login_title' => $config['login_title'] ?? '',
            // 登录描述
            'login_desc' => $config['login_desc'] ?? '',
            // 背景图片
            'bg' => $config['bg'] ?? '',
            // 广告图片
            'ad' => $config['ad'] ?? '',
            // 注册页面链接
            'register' => $config['register'] ?? '',
            // 忘记密码链接
            'findpwd' => $config['findpwd'] ?? '',
            // 返回链接
            'back_url' => $config['back_url'] ?? '',
            // 其他登录方式
            'other_login' => $config['other_login'] ?? $this->otherLogin,
        ];
        return $this;
    }

    /**
     * 公共接口
     * @param array $config
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function publicApi(array $config = [])
    {
        // 是否开启验证码
        $isCaptcha = $config['captcha'] ?? false;
        if (!$isCaptcha) {
            $isCaptcha = '20';
        }
        $captchaUrl = '';
        if ($isCaptcha && class_exists('Webman\Captcha\CaptchaBuilder')) {
            $captchaUrl = Url::make('Publics/captcha')->get();
        }
        $this->publicApi = [
            // 验证码接口
            'captcha' => $captchaUrl,
            // 登录接口
            'login' => $config['login'] ?? Url::make('Publics/login')
                ->module($this->module),
            // 退出接口
            'loginout' => $config['loginout'] ?? Url::make('Publics/loginout')
                ->module($this->module),
            // 获取用户信息
            'user' => $config['user'] ?? Url::make('Publics/user')
                ->module($this->module),
            // 获取菜单
            'menus' => $config['menus'] ?? Url::make('Publics/menus')
                ->module($this->module),
            // 获取主题布局
            'layouts' => $config['layouts'] ?? Url::make('Publics/layouts')
                ->module($this->module),
        ];
        return $this;
    }

    /**
     * 公用视图
     * @param array $config
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function publicView(array $config = [])
    {
        $this->publicView = [
            // 自定义登录页面
            'login' => $config['login'] ?? '',
            // 修改用户资料页面
            'user' => $config['user'] ?? Url::make('Admin/profile')
                ->module($this->module),
            // 工具栏视图
            'toolbar' => $config['toolbar'] ?? $this->getViewUrl('toolbar'),
            // 工作视图接口
            'workbench' => $config['workbench'] ?? $this->getViewUrl('workbench'),
        ];
        return $this;
    }

    /**
     * 获取公用视图接口
     * @param string $type
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getViewUrl(string $type)
    {
        return Url::make("Index/{$type}")->module($this->module);
    }

    /**
     * 上传分类接口
     * @param array $config
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function uploadCateApi(array $config = [])
    {
        $this->uploadCateApi = [
            'index' => $config['index'] ?? Url::make('Category/index')
                ->plugin('xbUpload')
                ->module($this->module)
                ->get(),
            'add' => $config['add'] ?? Url::make('Category/add')
                ->plugin('xbUpload')
                ->module($this->module)
                ->get(),
            'edit' => $config['edit'] ?? Url::make('Category/edit')
                ->plugin('xbUpload')
                ->module($this->module)
                ->get(),
            'del' => $config['del'] ?? Url::make('admin/Category/del')
                ->plugin('xbUpload')
                ->module($this->module)
                ->get(),
        ];
        return $this;
    }

    /**
     * 附件接口
     * @param array $config
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function uploadApi(array $config = [])
    {
        $this->uploadApi = [
            'index' => $config['index'] ?? Url::make('Upload/index')
                ->plugin('xbUpload')
                ->module($this->module)
                ->get(),
            'upload' => $config['upload'] ?? Url::make('Upload/upload')
                ->plugin('xbUpload')
                ->module($this->module)
                ->get(),
            'chunk' => $config['chunk'] ?? Url::make('Upload/chunk')
                ->plugin('xbUpload')
                ->module($this->module)
                ->get(),
            'edit' => $config['edit'] ?? Url::make('Upload/edit')
                ->plugin('xbUpload')
                ->module($this->module)
                ->get(),
            'move' => $config['move'] ?? Url::make('Upload/move')
                ->plugin('xbUpload')
                ->module($this->module)
                ->get(),
            'del' => $config['del'] ?? Url::make('Upload/del')
                ->plugin('xbUpload')
                ->module($this->module)
                ->get(),
        ];
        return $this;
    }

    /**
     * 编辑器上传接口
     * @param array $config
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function editorUploadApi(array $config = [])
    {
        $this->editorUploadApi = [
            'image' => $config['image'] ?? Url::make('Editor/image')
                ->plugin('xbUpload')
                ->module($this->module)
                ->get(),
            'video' => $config['upload'] ?? Url::make('Editor/video')
                ->plugin('xbUpload')
                ->module($this->module)
                ->get(),
            'file' => $config['upload'] ?? Url::make('Editor/file')
                ->plugin('xbUpload')
                ->module($this->module)
                ->get(),
        ];
        return $this;
    }

    /**
     * 远程组件
     * 示例
     * [
     *     [
     *         'title' => '用户注册',
     *         'path' => 'register',
     *     ],
     * ]
     * @param array $config
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function components(array $config = [])
    {
        $this->components = $config ?: $this->components;
        return $this;
    }

    /**
     * 批量添加全局组件
     * @param array $components 全部组件数据
     * - `name` 组件名称
     * - `component` 组件字符串
     * @return void
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    public function globalComponents(array $components)
    {
        foreach ($components as $value) {
            $this->globalComponent($value);
        }
    }

    /**
     * 添加全局组件
     * @param array $component 组件数据
     * - `name` 组件名称
     * - `component` 组件字符串
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function globalComponent(array $component)
    {
        if (empty($component['name'])) {
            throw new Exception('组件名称不能为空');
        }
        if (empty($component['component'])) {
            throw new Exception('组件模板字符串不能为空');
        }
        $this->global_components[] = $component;
        return $this;
    }

    /**
     * 设置扩展图标库
     * @param array $icons
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setIconsLinks(array $icons)
    {
        $this->icons_links = $icons;
        return $this;
    }

    /**
     * 获取站点配置
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function get()
    {
        $data = [];
        // 系统信息
        $data = array_merge($data, $this->system);
        // 组织及备案信息
        $data = array_merge($data, $this->webIcp);
        // 版本信息
        $data['web_version'] = PluginsApi::make()->get('xbCode')['version'] ?? '';
        // 登录页数据
        $data['login_data'] = $this->loginData;
        // 公用接口
        $data['public_api'] = $this->publicApi;
        // 公用视图
        $data['public_view'] = $this->publicView;
        // 附件分类接口
        $data['upload_cate_api'] = $this->uploadCateApi;
        // 附件接口
        $data['upload_api'] = $this->uploadApi;
        // 编辑器上传接口
        $data['editor_upload_api'] = $this->editorUploadApi;
        // 远程组件
        $data['components'] = $this->components;
        // 全局组件
        $data['global_components'] = $this->global_components;
        // 扩展图标库
        $data['icons_links'] = $this->icons_links;
        // 返回数据
        return $data;
    }

    /**
     * 获取字段值
     * @param string $field
     * @return mixed
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getField(string $field)
    {
        return $this->$field;
    }

    /**
     * JSON序列化
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function jsonSerialize(): mixed
    {
        return $this->get();
    }
}