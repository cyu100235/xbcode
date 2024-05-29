<?php
namespace app\admin\validate;

use Tinywan\Validate\Validate;

class DictValidate extends Validate
{
    protected array $rule = [
        'title' => 'require',
        'name' => 'require|alphaNum',
        'content' => 'require|verifyContent',
    ];

    protected array $message = [
        'title.require' => '请输入字典名称',
        'name.require' => '请输入字典标识',
        'name.alphaNum' => '字典标识只能为字母和数字',
        'content.require' => '请输入字典内容',
    ];

    /**
     * 验证字典内容
     * @param mixed $value
     * @return bool|string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function verifyContent($value)
    {
        $content = explode("\n", $value);
        $content = array_filter($content);
        if (empty($content)) {
            return '字典内容必须一行一条';
        }
        return true;
    }
}
