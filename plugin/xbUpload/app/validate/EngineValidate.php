<?php
namespace plugin\xbUpload\app\validate;

use taoser\Validate;

/**
 * 引擎验证
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class EngineValidate extends Validate
{
    protected $rule = [
        'title' => 'require',
        'name' => 'require',
        'plugin' => 'require',
        'desc' => 'require',
        'prompt' => 'require',
    ];

    protected $message = [
        'title.require' => '引擎名称参数错误',
        'name.require' => '引擎标识参数错误',
        'template.require' => '引擎插件参数错误',
        'desc.require' => '引擎描述参数错误',
        'prompt.require' => '引擎提示词参数错误',
    ];
}
