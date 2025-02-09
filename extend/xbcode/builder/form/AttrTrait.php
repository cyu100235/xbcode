<?php
namespace xbcode\builder\form;

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
        $config = $this->builder->formConfig();
        $config = array_merge($config, [
            'form' =>[
                'labelPosition' => $position,
                'labelWidth' => $width
            ]
        ]);
        $this->builder->setConfig($config);
        return $this;
    }

    /**
     * 设置表单宽度
     * @param string $width 表单宽度
     * @param string $position 表单位置 center居中 flex-start左对齐 flex-end右对齐
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function setFormWidth(string $width = '100%', string $position = 'center')
    {
        $config = $this->builder->formConfig();
        $config = array_merge($config, [
            'form_width' => $width,
            'form_position' => $position
        ]);
        $this->builder->setConfig($config);
        return $this;
    }

    /**
     * 设置配置
     * @param array $data
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function config(array $data)
    {
        $config = $this->builder->formConfig();
        $config = array_merge($config, $data);
        $this->builder->setConfig($config);
    }
}