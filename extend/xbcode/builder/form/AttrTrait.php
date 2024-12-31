<?php

namespace xbcode\builder\form;

use xbcode\builder\FormBuilder;
use FormBuilder\Form;

/**
 * 表单联动
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait AttrTrait
{
    /**
     * 表单对象
     * @var Form
     */
    private $builder;
    
    /**
     * 设置标签方向
     * @param string $position
     * @param mixed $width
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function setPosition(string $position = 'left', mixed $width = 80)
    {
        $this->builder->setConfig([
            'form' =>[
                'labelPosition' => $position,
                'labelWidth' => $width
            ]
        ]);
        return $this;
    }
}