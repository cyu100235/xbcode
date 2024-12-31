<?php
namespace app\validate;

use Tinywan\Validate\Validate;

/**
 * 字典数据验证器
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class DictDataValidate extends Validate
{
    protected array $rule = [
        'dict_id' => 'require',
        'label' => 'require',
        'value' => 'require',
    ];

    protected array $message = [
        'dict_id.require' => '字典参数错误',
        'label.require' => '请输入数据名称',
        'value.require' => '请输入数据参数',
    ];

    /**
     * 添加场景验证
     * @return DictDataValidate
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function sceneAdd()
    {
        return $this
            ->only([
                'label',
                'value',
            ]);
    }

    /**
     * 编辑场景验证
     * @return DictDataValidate
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function sceneEdit()
    {
        return $this
            ->only([
                'label',
                'value',
            ]);
    }
}
