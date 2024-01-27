<?php

namespace app\common\service;

class AppService
{
    /**
     * 返回应用数据
     * @param array $data
     * @return array
     * @author John
     */
    public static function resutl(array $data)
    {
        $moduleRoute = getModuleRoute();
        $toolbar     = '/remote/toolbar';
        if ($moduleRoute !== '/admin') {
            $toolbar = "{$moduleRoute}/remote/toolbar";
        }
        // 返回数据
        $data       = [
            'web_name'              => $data['web_name']??'XB-Base',
            'web_title'             => $data['web_title']??'后台登录',
            'web_logo'              => $data['web_logo']??'',
            'version_name'          => $data['version_name']??'',
            'version'               => $data['version']??'',
            // 版权token
            'empower_token'         => $data['empower_token']??'',
            // 版权私钥
            'empower_private_key'   => $data['empower_private_key']??'',
            // 登录页链接
            'login_link'            => [
                'register'          => $data['login_link_register']??'',
                'forget'            => $data['login_link_forget']??'',
                'other_login'       => $data['login_link_other_login']??[],
            ],
            // 公用接口
            'public_api'            => [
                // 登录接口
                'login'             => $data['public_api_login']??"{$moduleRoute}/Login/login",
                // 自定义登录页
                'login_file'        => $data['public_api_login_file']??'',
                // 退出接口
                'loginout'          => $data['public_api_loginout']??"{$moduleRoute}/Login/loginout",
                // 获取用户信息
                'user'              => $data['public_api_user']??"{$moduleRoute}/Login/user",
                // 清除缓存
                'clear'             => $data['public_api_clear']??"{$moduleRoute}/Index/clear",
                // 锁定页面
                'lock'              => $data['public_api_lock']??"{$moduleRoute}/Index/lock",
                // 修改登录者信息
                "user_edit"         => $data['public_api_user_edit']??"{$moduleRoute}/Admin/info",
                // 头部toolBar远程文件
                "header_right_file" => $data['public_api_header_right_file']??$toolbar,
            ],
            // 远程组件
            'remote_url'            => $data['remote_url']??[],
            // 附件库API
            'uploadify_api'         => [
                'index'             => $data['uploadify_api_index']??"{$moduleRoute}/Upload/index",
                'upload'            => $data['uploadify_api_upload']??"{$moduleRoute}/Upload/upload",
                'edit'              => $data['uploadify_api_edit']??"{$moduleRoute}/Upload/edit",
                'del'               => $data['uploadify_api_del']??"{$moduleRoute}/Upload/del",
                'move'              => $data['uploadify_api_move']??"{$moduleRoute}/Upload/move",
            ],
            // 附件库分类
            'uploadify_cate'        => [
                'index'             => $data['uploadify_cate_index']??"{$moduleRoute}/UploadCate/index",
                'add'               => $data['uploadify_cate_add']??"{$moduleRoute}/UploadCate/add",
                'edit'              => $data['uploadify_cate_edit']??"{$moduleRoute}/UploadCate/edit",
                'del'               => $data['uploadify_cate_del']??"{$moduleRoute}/UploadCate/del",
            ],
        ];
        return $data;
    }
}
