<?php
namespace app\validate;

use app\model\DictTag;
use Tinywan\Validate\Validate;

class DictValidate extends Validate
{
    protected array $rule = [
        'title' => 'require',
        'name' => 'require|alphaNum|verifyunique',
    ];

    protected array $message = [
        'title.require' => '请输入字典名称',
        'name.require' => '请输入字典标识',
        'name.unique' => '字典标识已存在',
        'name.alphaNum' => '字典标识只能为字母和数字',
    ];

    /**
     * 添加场景验证
     * @return DictValidate
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function sceneAdd()
    {
        return $this
            ->only([
                'title',
                'name',
            ]);
    }

    /**
     * 编辑场景验证
     * @return DictValidate
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function sceneEdit()
    {
        return $this
            ->only([
                'title',
                'name',
            ])
            ->remove('name', ['verifyunique']);
    }

    /**
     * 验证字典标识
     * @param mixed $value
     * @return bool|string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function verifyunique($value)
    {
        $where  = [
            ['name', '=', $value],
        ];
        $result = DictTag::where($where)->find();
        if ($result) {
            return '该字典标识已存在';
        }
        return true;
    }
}
