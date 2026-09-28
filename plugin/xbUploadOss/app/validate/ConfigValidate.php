<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbUploadOss\app\validate;

use Webman\Validation\Validator;

/**
 * 储存配置验证器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class ConfigValidate extends Validator
{
    /**
     * 验证规则
     * @var array
     */
    protected array $rules = [
        'bucket' => 'required',
        'domain' => 'required|url',
        'access_key' => 'required',
        'secret_key' => 'required',
    ];

    /**
     * 错误提示
     * @var array
     */
    protected array $messages = [
        'bucket.required' => '请输入空间名称',
        'domain.required' => '请输入域名链接',
        'domain.url' => '请输入正确的域名链接',
        'access_key.required' => '请输入ACCESS_KEY',
        'secret_key.required' => '请输入SECRET_KEY',
    ];
}