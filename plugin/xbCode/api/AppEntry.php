<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @version  1.0
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\api;

use JsonSerializable;

/**
 * 应用入口接口类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class AppEntry implements JsonSerializable
{
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
     * 实例化
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function make()
    {
        $class = new static;
        $class->system();
        $class->webIcp();
        $class->otherLogin();
        $class->loginData();
        $class->publicApi();
        $class->publicView();
        $class->uploadCateApi();
        $class->uploadApi();
        $class->editorUploadApi();
        $class->components();
        return $class;
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
        if (empty($config)) {
            $config = ConfigApi::make('system')->get('', []);
        }
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
        $this->webIcp = $config;
        return $this;
    }

    /**
     * 其他登录方式
     * 示例：
     * [
     *     [
     *         'title' => '微信登录',
     *         'icon' => 'wechat',
     *         'url' => 'https://www.xbcode.net/wechat/login',
     *     ],
     * ]
     * @param array $config
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function otherLogin(array $config = [])
    {
        $this->otherLogin = $config ?: [];
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
            $captcha = ConfigApi::make('system')->get('captcha_state', '10');
            $isCaptcha = $captcha === '20';
        }
        $captchaUrl = '';
        if ($isCaptcha && class_exists('Webman\Captcha\CaptchaBuilder')) {
            $captchaUrl = xbUrl('Publics/captcha');
        }
        $this->publicApi = [
            // 验证码接口
            'captcha' => $captchaUrl,
            // 登录接口
            'login' => $config['login'] ?? xbUrl('Publics/login'),
            // 退出接口
            'loginout' => $config['loginout'] ?? xbUrl('Publics/loginout'),
            // 获取用户信息
            'user' => $config['user'] ?? xbUrl('Publics/user'),
            // 获取菜单
            'menus' => $config['menus'] ?? xbUrl('Publics/menus'),
            // 获取主题布局
            'layouts' => $config['layouts'] ?? xbUrl('Publics/layouts'),
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
            'user' => $config['user'] ?? xbUrl('Admin/profile'),
            // 工具栏视图
            'toolbar' => $config['toolbar'] ?? xbUrl('Index/toolbar'),
            // 工作视图接口
            'workbench' => $config['workbench'] ?? xbUrl('Index/workbench'),
        ];
        return $this;
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
            'index' => $config['index'] ?? xbUrl('admin/Category/index', [], ['plugin' => 'xbUpload']),
            'add' => $config['add'] ?? xbUrl('admin/Category/add', [], ['plugin' => 'xbUpload']),
            'edit' => $config['edit'] ?? xbUrl('admin/Category/edit', [], ['plugin' => 'xbUpload']),
            'del' => $config['del'] ?? xbUrl('admin/Category/del', [], ['plugin' => 'xbUpload']),
        ];
        return $this;
    }

    /**
     * 附件接口
     * @param array $config
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function uploadApi(array $config = [])
    {
        $this->uploadApi = [
            'index' => $config['index'] ?? xbUrl('admin/Upload/index', [], ['plugin' => 'xbUpload']),
            'upload' => $config['upload'] ?? xbUrl('admin/Upload/upload', [], ['plugin' => 'xbUpload']),
            'chunk' => $config['chunk'] ?? xbUrl('admin/Upload/chunk', [], ['plugin' => 'xbUpload']),
            'edit' => $config['edit'] ?? xbUrl('admin/Upload/edit', [], ['plugin' => 'xbUpload']),
            'move' => $config['move'] ?? xbUrl('admin/Upload/move', [], ['plugin' => 'xbUpload']),
            'del' => $config['del'] ?? xbUrl('admin/Upload/del', [], ['plugin' => 'xbUpload']),
        ];
        return $this;
    }

    /**
     * 编辑器上传接口
     * @param array $config
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function editorUploadApi(array $config = [])
    {
        $this->editorUploadApi = [
            'image' => $config['image'] ?? xbUrl('admin/Editor/image',[], ['plugin' => 'xbUpload']),
            'video' => $config['upload'] ?? xbUrl('admin/Editor/video',[], ['plugin' => 'xbUpload']),
            'file' => $config['upload'] ?? xbUrl('admin/Editor/file',[], ['plugin' => 'xbUpload']),
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
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function components(array $config = [])
    {
        $this->components = $config ?: $this->components;
        return $this;
    }

    /**
     * 获取站点配置
     * @param array $config 配置数据
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
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
        // 返回数据
        return $data;
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