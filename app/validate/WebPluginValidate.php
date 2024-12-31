<?php
namespace app\validate;

use Tinywan\Validate\Validate;

/**
 * 站点插件授权验证器
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class WebPluginValidate extends Validate
{
    protected array $rule = [
        'name' => 'require',
        'expire_time' => 'require',
    ];

    protected array $message = [
        'name.require' => '请选择授权插件',
        'expire_time.require' => '请选择到期时间',
    ];
}
