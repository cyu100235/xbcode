<?php

namespace xbcode\builder\table\attrs;
use xbcode\builder\ListBuilder;

/**
 * 选项卡表格
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait TabsTrait
{
    /**
     * 选项卡配置
     * @var array
     */
    protected $tabsConfig = [
        'active' => '',
        'field'  => '',
        'list'   => [
            // [
            //     'label' => '选项卡1',
            //     'value' => '10',
            // ],
        ],
    ];

    /**
     * 设置选项卡表格
     * @Author 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-03-15
     * @param  array       $config
     * @return ListBuilder
     */
    public function setTabs(array $data,string $field,string $active):ListBuilder
    {
        $this->tabsConfig = array_merge($this->tabsConfig,[
            'active' => $active,
            'field'  => $field,
            'list'   => $data,
        ]);
        return $this;
    }
}