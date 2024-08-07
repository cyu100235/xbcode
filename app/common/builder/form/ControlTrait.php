<?php

namespace app\common\builder\form;

use app\common\builder\FormBuilder;
use FormBuilder\Form;

/**
 * 表单联动
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait ControlTrait
{
    /**
     * 表单对象
     * @var Form
     */
    private $builder;

    /**
     * 添加表单联动元素
     * @param string $field
     * @param string $type
     * @param string $title
     * @param mixed $value
     * @param array $extra
     * @return FormBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function addControl(string $field, string $type, string $title, mixed $value, array $rule, array $extra = []): FormBuilder
    {
        $extra['control'] = $rule;
        return $this->addRow($field, $type, $title, $value, $extra);
    }
}