<?php
namespace app\admin\validate;

use app\model\Dict;
use Tinywan\Validate\Validate;

class DictValidate extends Validate
{
    protected array $rule = [
        'title' => 'require',
        'name' => 'require|alphaNum|verifyunique',
        'content' => 'require|verifyContent',
    ];

    protected array $message = [
        'title.require' => '请输入字典名称',
        'name.require' => '请输入字典标识',
        'name.unique' => '字典标识已存在',
        'name.alphaNum' => '字典标识只能为字母和数字',
        'content.require' => '请输入字典内容',
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
                'content',
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
                'content',
            ])
            ->remove('name', ['verifyunique']);
    }

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
        $result = Dict::where($where)->find();
        if ($result) {
            return '该字典标识已存在';
        }
        return true;
    }
}
