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
     * 获取站点配置
     * @param array $config 配置数据
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function get(array $config = [])
    {
        // 获取应用名称
        $moduleName = xbAppName();
        // 基础信息
        $data['web_url']   = $config['web_url'] ?? '';
        $data['web_name']   = $config['web_name'] ?? 'xbcode';
        $data['web_title']  = $config['web_title'] ?? '后台登录';
        $data['web_desc']   = $config['web_description'] ?? '';
        $data['web_logo']   = empty($config['web_logo']) ? '' : $config['web_logo'];
        $data['workbench']  = $config['workbench'] ?? '';
        // 备案信息
        $loginBeian = config('projects.system_info', []);
        // 登录页备案信息
        $data['login_beian'] = [
            // 组织信息
            'about_name' => $config['login_beian']['about_name'] ?? $loginBeian['about_name'],
            'about_url' => $config['login_beian']['about_url'] ?? $loginBeian['about_url'],
            // 系统信息
            'system_name' => $config['login_beian']['system_name'] ?? $loginBeian['system_name'],
            'system_url' => $config['login_beian']['system_url'] ?? $loginBeian['system_url'],
            'system_version' => $config['login_beian']['system_version'] ?? $loginBeian['system_version'],
            // 备案编号
            'beian_text' => $config['login_beian']['beian_text'] ?? $loginBeian['beian_text'],
            'beian_url' => $config['login_beian']['beian_url'] ?? $loginBeian['beian_url'],
            // 公安备案
            'police_beian_text' => $config['login_beian']['police_beian_text'] ?? $loginBeian['police_beian_text'],
            'police_beian_url' => $config['login_beian']['police_beian_url'] ?? $loginBeian['police_beian_url'],
        ];
        // 登录页数据
        $data['login_data'] = [
            // 登录标题
            'login_title' => $config['login_data']['login_title'] ?? '',
            // 登录描述
            'login_desc' => $config['login_data']['login_desc'] ?? '',
            // 背景图片
            'bg' => $config['login_data']['bg'] ?? '',
            // 广告图片
            'ad' => $config['login_data']['ad'] ?? '',
            // 注册页面链接
            'register' => $config['login_link']['register'] ?? '',
            // 忘记密码链接
            'findpwd' => $config['login_link']['forget'] ?? '',
            // 其他登录方式
            'other_login' => $config['login_link']['other_login'] ?? [],
        ];
        // 是否开启验证码
        $captcha = xbEnv('CAPTCHA_LOGIN', true);
        // 公用接口
        $data['public_api'] = [
            // 验证码接口
            'captcha' => $captcha ? xbUrl('Login/captcha', [], true) : '',
            // 登录接口
            'login' => $config['public_api']['login'] ?? "{$moduleName}/Login/login",
            // 退出接口
            'loginout' => $config['public_api']['loginout'] ?? "{$moduleName}/Login/loginout",
            // 获取用户信息
            'user' => $config['public_api']['user'] ?? "{$moduleName}/Login/user",
            // 获取菜单
            'menu' => $config['public_api']['menu'] ?? "{$moduleName}/Login/menus",
        ];
        // 公用视图
        $data['public_view'] = [
            // 登录页
            'login' => $config['public_view']['login'] ?? '',
            // 用户中心
            'user' => $config['public_view']['user'] ?? "{$moduleName}/Admin/profile",
            // 工具栏视图
            'toolbar' => $config['public_view']['toolbar'] ?? '',
        ];
        // 附件分类接口
        $data['upload_cate_api'] = [
            'index' => $config['upload_cate_api']['index'] ?? "{$moduleName}/UploadCate/index",
            'add' => $config['upload_cate_api']['add'] ?? "{$moduleName}/UploadCate/add",
            'edit' => $config['upload_cate_api']['edit'] ?? "{$moduleName}/UploadCate/edit",
            'del' => $config['upload_cate_api']['del'] ?? "{$moduleName}/UploadCate/del",
        ];
        // 附件接口
        $data['upload_api'] = [
            'index' => $config['uploadify_api']['index'] ?? "{$moduleName}/Upload/index",
            'upload' => $config['uploadify_api']['upload'] ?? "{$moduleName}/Upload/upload",
            'chunk' => $config['uploadify_api']['chunk'] ?? "{$moduleName}/Upload/chunk",
            'edit' => $config['uploadify_api']['edit'] ?? "{$moduleName}/Upload/edit",
            'move' => $config['uploadify_api']['move'] ?? "{$moduleName}/Upload/move",
            'del' => $config['uploadify_api']['del'] ?? "{$moduleName}/Upload/del",
        ];
        // 编辑器上传接口
        $data['editor_upload'] = [
            'image' => $config['editor_upload']['image'] ?? "{$moduleName}/Editor/image",
            'video' => $config['editor_upload']['upload'] ?? "{$moduleName}/Editor/video",
            'file' => $config['editor_upload']['upload'] ?? "{$moduleName}/Editor/file",
        ];
        // 返回数据
        return $data;
    }
}