<?php

namespace xbcode\providers;

/**
 * 站点配置提供者
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class AppProvider
{
    /**
     * 版权及备案信息
     * @param array $config
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function copyrightBeian(array $config = [])
    {
        // 获取版权配置
        $beian             = ConfigProvider::get('backend/copyright');
        $about_name        = $beian['about_name'] ?? '';
        $about_url         = $beian['about_url'] ?? '';
        $system_name       = $beian['system_name'] ?? '';
        $system_url        = $beian['system_url'] ?? '';
        $system_version    = config('projects.version_name', '1.0.0');
        $beian_text        = $beian['beian_text'] ?? '';
        $beian_url         = $beian['beian_url'] ?? '';
        $police_beian_text = $beian['police_beian_text'] ?? '';
        $police_beian_url  = $beian['police_beian_url'] ?? '';
        return [
            // 组织信息
            'about_name' => $config['login_beian']['about_name'] ?? $about_name,
            'about_url' => $config['login_beian']['about_url'] ?? $about_url,
            // 系统信息
            'system_name' => $config['login_beian']['system_name'] ?? $system_name,
            'system_url' => $config['login_beian']['system_url'] ?? $system_url,
            'system_version' => $config['login_beian']['system_version'] ?? $system_version,
            // 备案编号
            'beian_text' => $config['login_beian']['beian_text'] ?? $beian_text,
            'beian_url' => $config['login_beian']['beian_url'] ?? $beian_url,
            // 公安备案
            'police_beian_text' => $config['login_beian']['police_beian_text'] ?? $police_beian_text,
            'police_beian_url' => $config['login_beian']['police_beian_url'] ?? $police_beian_url,
        ];
    }

    /**
     * 登录页数据
     * @param array $config
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function loginData(array $config = [])
    {
        return [
            // 登录标题
            'login_title' => $config['login_data']['login_title'] ?? '',
            // 登录描述
            'login_desc' => $config['login_data']['login_desc'] ?? '',
            // 背景图片
            'bg' => $config['login_data']['bg'] ?? '',
            // 广告图片
            'ad' => $config['login_data']['ad'] ?? '',
            // 注册页面链接
            'register' => $config['login_data']['register'] ?? '',
            // 忘记密码链接
            'findpwd' => $config['login_data']['forget'] ?? '',
            // 返回链接
            'back_url' => $config['login_data']['back_url'] ?? '',
            // 其他登录方式
            'other_login' => $config['login_data']['other_login'] ?? [],
        ];
    }
    
    /**
     * 公共接口
     * @param array $config
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function publicApi(array $config = [])
    {
        // 是否开启验证码
        $captcha = xbEnv('CAPTCHA_LOGIN', true);
        $captchaUrl = '';
        if ($captcha) {
            $captchaUrl = $config['public_api']['captcha'] ?? xbUrl('Publics/captcha');
        }
        return [
            // 验证码接口
            'captcha' => $captchaUrl,
            // 登录接口
            'login' => $config['public_api']['login'] ?? xbUrl('Publics/login'),
            // 退出接口
            'loginout' => $config['public_api']['loginout'] ?? xbUrl('Publics/loginout'),
            // 获取用户信息
            'user' => $config['public_api']['user'] ?? xbUrl('Publics/user'),
            // 获取菜单
            'menu' => $config['public_api']['menu'] ?? xbUrl('Publics/menus'),
        ];
    }

    /**
     * 公共视图
     * @param array $config
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function publicView(array $config = [])
    {
        return [
            // 登录页
            'login' => $config['public_view']['login'] ?? '',
            // 用户中心
            'user' => $config['public_view']['user'] ?? xbUrl('Admin/profile'),
            // 工具栏视图
            'toolbar' => $config['public_view']['toolbar'] ?? '',
        ];
    }
    
    /**
     * 附件分类接口
     * @param array $config
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function uploadCateApi(array $config = [])
    {
        return [
            'index' => $config['upload_cate_api']['index'] ?? xbUrl('UploadCate/index'),
            'add' => $config['upload_cate_api']['add'] ?? xbUrl('UploadCate/add'),
            'edit' => $config['upload_cate_api']['edit'] ?? xbUrl('UploadCate/edit'),
            'del' => $config['upload_cate_api']['del'] ?? xbUrl('UploadCate/del'),
        ];
    }

    /**
     * 附件接口
     * @param array $config
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function uploadApi(array $config = [])
    {
        return [
            'index' => $config['uploadify_api']['index'] ?? xbUrl('Upload/index'),
            'upload' => $config['uploadify_api']['upload'] ?? xbUrl('Upload/upload'),
            'chunk' => $config['uploadify_api']['chunk'] ?? xbUrl('Upload/chunk'),
            'edit' => $config['uploadify_api']['edit'] ?? xbUrl('Upload/edit'),
            'move' => $config['uploadify_api']['move'] ?? xbUrl('Upload/move'),
            'del' => $config['uploadify_api']['del'] ?? xbUrl('Upload/del'),
        ];
    }

    /**
     * 编辑器上传接口
     * @param array $config
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function editorUploadApi(array $config = [])
    {
        return [
            'image' => $config['editor_upload']['image'] ?? xbUrl('Editor/image'),
            'video' => $config['editor_upload']['upload'] ?? xbUrl('Editor/video'),
            'file' => $config['editor_upload']['upload'] ?? xbUrl('Editor/file'),
        ];
    }

    /**
     * 远程组件
     * @param array $config
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function components(array $config = [])
    {
        $data = $config['components'] ?? [
            // [
            //     'title' => '用户注册',
            //     'path' => 'register',
            //     'api' => xbUrl('Publics/register'),
            // ],
        ];
        return $data;
    }

    /**
     * 获取站点配置
     * @param array $config 配置数据
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function get(array $config = [])
    {
        // 基础信息
        $data['web_url']   = $config['web_url'] ?? '';
        $data['web_name']  = $config['web_name'] ?? 'xbcode';
        $data['web_title'] = $config['web_title'] ?? '后台登录';
        $data['web_desc']  = $config['web_description'] ?? '';
        $data['web_logo']  = empty($config['web_logo']) ? '' : $config['web_logo'];
        $data['workbench'] = $config['workbench'] ?? '';
        // 备案信息
        $data['login_beian'] = self::copyrightBeian($config);
        // 登录页数据
        $data['login_data'] = self::loginData($config);
        // 公用接口
        $data['public_api'] = self::publicApi($config);
        // 公用视图
        $data['public_view'] = self::publicView($config);
        // 附件分类接口
        $data['upload_cate_api'] = self::uploadCateApi($config);
        // 附件接口
        $data['upload_api'] = self::uploadApi($config);
        // 编辑器上传接口
        $data['editor_upload_api'] = self::editorUploadApi($config);
        // 远程组件
        $data['components'] = self::components($config);
        // 返回数据
        return $data;
    }
}