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
    public static function resutl(array $data,)
    {
        $moduleName = request()->module_name;
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
                'login'             => $data['public_api_login']??"{$moduleName}/Users/login",
                // 自定义登录页
                'login_file'        => $data['public_api_login_file']??'',
                // 退出接口
                'loginout'          => $data['public_api_loginout']??"{$moduleName}/Users/loginout",
                // 获取用户信息
                'user'              => $data['public_api_user']??"{$moduleName}/Users/index",
                // 清除缓存
                'clear'             => $data['public_api_clear']??"{$moduleName}/Index/clear",
                // 锁定页面
                'lock'              => $data['public_api_lock']??"{$moduleName}/Index/lock",
                // 修改登录者信息
                "user_edit"         => $data['public_api_user_edit']??"{$moduleName}/Admin/info",
                // 头部toolBar远程文件
                "header_right_file" => $data['public_api_header_right_file']??"remote/headerToolbar",
            ],
            // 远程组件
            'remote_url'            => $data['remote_url']??[],
            // 附件库API
            'uploadify_api'         => [
                'index'             => $data['uploadify_api_index']??"{$moduleName}/Upload/index",
                'upload'            => $data['uploadify_api_upload']??"{$moduleName}/Upload/upload",
                'edit'              => $data['uploadify_api_edit']??"{$moduleName}/Upload/edit",
                'del'               => $data['uploadify_api_del']??"{$moduleName}/Upload/del",
                'move'              => $data['uploadify_api_move']??"{$moduleName}/Upload/move",
            ],
            // 附件库分类
            'uploadify_cate'        => [
                'index'             => $data['uploadify_cate_index']??"{$moduleName}/UploadCate/index",
                'add'               => $data['uploadify_cate_add']??"{$moduleName}/UploadCate/add",
                'edit'              => $data['uploadify_cate_edit']??"{$moduleName}/UploadCate/edit",
                'del'               => $data['uploadify_cate_del']??"{$moduleName}/UploadCate/del",
            ],
        ];
        return $data;
    }
}
