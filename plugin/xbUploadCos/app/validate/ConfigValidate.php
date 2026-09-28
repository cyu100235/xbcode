<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbUploadCos\app\validate;

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
        'region' => 'required',
        'domain' => 'required|url',
        'secret_id' => 'required',
        'secret_key' => 'required',
    ];

    /**
     * 错误提示
     * @var array
     */
    protected array $messages = [
        'bucket.required' => '请输入空间名称',
        'region.required' => '请输入所属地域',
        'domain.required' => '请输入域名链接',
        'domain.url' => '请输入正确的域名链接',
        'secret_id.required' => '请输入SECRET_Id',
        'secret_key.required' => '请输入SECRET_KEY',
    ];
}