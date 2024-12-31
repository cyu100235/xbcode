<?php
namespace app\validate;

use Tinywan\Validate\Validate;

/**
 * 站点公告验证器
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class WebNoticeValidate extends Validate
{
    protected array $rule = [
        'title' => 'require',
    ];

    protected array $message = [
        'title.require' => '请输入公告标题',
    ];
}
