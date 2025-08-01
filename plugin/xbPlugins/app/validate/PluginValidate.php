<?php
namespace plugin\xbPlugins\app\validate;

use taoser\Validate;

/**
 * 插件验证器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class PluginValidate extends Validate
{
    protected $rule = [
        'title' => 'require',
        'name' => 'require',
        'version' => 'require',
        'author' => 'require',
        'desc' => 'require',
    ];

    protected $message = [
        'title.require' => '插件名称参数错误',
        'name.require' => '插件标识参数错误',
        'version.require' => '版本名称参数错误',
        'author.require' => '插件作者参数错误',
        'desc.require' => '插件描述参数错误',
    ];
}
