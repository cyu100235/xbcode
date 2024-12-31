<?php

namespace xbcode\builder\form;

use xbcode\builder\FormBuilder;

/**
 * 表单验证
 * @author 贵州小白基地网络科技有限公司
 * @copyright (c) 2023
 */
trait FormValidateTrait
{
    /**
     * 表单验证
     * @param mixed $validate
     * @return FormBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-04-29
     */
    public function formValidate($validate): FormBuilder
    {
        /**
         * 实例验证类
         * @var Validate
         */
        $class = new $validate;
        return $this;
    }
}