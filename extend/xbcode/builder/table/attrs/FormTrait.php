<?php

namespace xbcode\builder\table\attrs;

use xbcode\builder\ListBuilder;

/**
 * 筛选表单查询
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait FormTrait
{
    /**
     * 筛选表单
     * @var array
     */
    protected $formConfig = [];
    
    /**
     * 筛选表单配置
     * @var array
     */
    protected $screenConfig = [
        [
            'type' => 'submit',
            'status' => 'primary',
            'content' => '查询',
        ],
        [
            'type' => 'reset',
            'content' => '重置',
        ]
    ];

    /**
     * 表格的表单配置
     * @Author 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-03-07
     * @param  array       $config
     * @return ListBuilder
     */
    public function formConfig(array $config = []): ListBuilder
    {
        $this->formConfig = array_merge([], $config);
        return $this;
    }

    /**
     * 设置启用远程组件表单
     * @param string $file
     * @param array $params
     * @return ListBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @email cy958416459@qq.com
     */
    public function addScreenRemote(string $file, array $params = []): ListBuilder
    {
        $this->screenRemote['file']       = $file;
        $this->screenRemote['ajaxParams'] = $params;
        return $this;
    }
    
    /**
     * 添加筛选单元格
     * @param string $field
     * @param string $type
     * @param string $title
     * @param mixed $value
     * @param array $extra
     * @param array $attrs
     * @return \xbcode\builder\ListBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function addScreen(string $field, string $type, string $title,mixed $value = null, array $extra = [], array $attrs = []): ListBuilder
    {
        if (!isset($this->formConfig['items'])) {
            $this->formConfig['data']  = [];
            $this->formConfig['items'] = [];
        }
        $plaholder = "请输入{$title}";
        if (in_array($type, ['select', 'radio', 'checkbox'])) {
            $plaholder = "请选择{$title}";
        }
        $item = [
            'field' => $field,
            'title' => $title,
            'itemRender' => [
                'name' => $type,
                'props' => [
                    'placeholder' => $plaholder
                ],
            ],
        ];
        // 合并属性
        if ($attrs && is_array($attrs)) {
            $item = array_merge($item, $attrs);
        }
        // 合并额外属性
        if ($extra && is_array($extra)) {
            $item['itemRender']['props'] = $extra;
        }
        array_push($this->formConfig['items'], $item);
        $this->formConfig['data'][$field] = $value;
        return $this;
    }

    /**
     * 设置提交按钮
     * @param array $config
     * @return mixed
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public function submitConfig(array $config)
    {
        $this->screenConfig = array_merge($this->screenConfig[0], $config);
        return $this;
    }

    /**
     * 设置重置按钮
     * @param array $config
     * @return mixed
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public function restConfig(array $config)
    {
        $this->screenConfig = array_merge($this->screenConfig[1], $config);
        return $this;
    }
}