<?php

namespace app\common\builder\table;
use app\common\builder\ListBuilder;

/**
 * 筛选查询
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait FormTrait
{
    // 筛选表单
    protected $formConfig = [];
    // 筛选表单配置
    protected $screenConfig = [
        [
            'props' => [
                'api' => '',
                'method' => 'GET',
                'type' => 'submit',
                'status' => 'primary',
                'content' => '查询',
            ],
        ],
        [
            'props' => [
                'api' => '',
                'method' => 'GET',
                'type' => 'reset',
                'status' => 'default',
                'content' => '重置',
            ],
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
     * @param array $extra
     * @return ListBuilder
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public function addScreen(string $field, string $type, string $title, array $extra = []): ListBuilder
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
                'name' => "\${$type}",
                'props' => [
                    'placeholder' => $plaholder
                ],
            ],
        ];
        if ($extra && is_array($extra)) {
            $item['itemRender']['props'] = $extra;
        }
        array_push($this->formConfig['items'], $item);
        $this->formConfig['data'][$field] = '';
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
        $this->screenConfig = array_merge($this->screenConfig[0]['props'], $config);
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
        $this->screenConfig = array_merge($this->screenConfig[1]['props'], $config);
        return $this;
    }
}