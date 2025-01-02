<?php
namespace app\validate;

use app\model\WebPlugin;
use Tinywan\Validate\Validate;

/**
 * 站点插件授权验证器
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class WebPluginValidate extends Validate
{
    protected array $rule = [
        'name' => 'require|verifyPluginAuth',
    ];

    protected array $message = [
        'name.require' => '请选择授权插件',
    ];

    /**
     * 验证插件授权
     * @param mixed $value
     * @param mixed $rule
     * @param mixed $data
     * @return bool|string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function verifyPluginAuth($value, $rule, $data = [])
    {
        if (empty($data['saas_appid'])) {
            return true;
        }
        $where  = [
            'name' => $value,
            'saas_appid' => $data['saas_appid'],
        ];
        $plugin = WebPlugin::where($where)->find();
        if ($plugin) {
            return '该插件已授权';
        }
        return true;
    }
}
