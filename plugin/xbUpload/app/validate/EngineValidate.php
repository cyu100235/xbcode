<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
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
        'desc' => 'require|max:230',
        'prompt' => 'require|max:230',
    ];

    protected $message = [
        'title.require' => '引擎名称参数错误',
        'name.require' => '引擎标识参数错误',
        'plugin.require' => '引擎插件参数错误',
        'desc.require' => '引擎描述参数错误',
        'desc.max' => '引擎描述字数不能超过230个字',
        'prompt.require' => '引擎描述词参数错误',
        'prompt.max' => '引擎描述词字数不能超过230个字',
    ];
}
