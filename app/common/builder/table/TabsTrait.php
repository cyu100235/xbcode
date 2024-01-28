<?php

namespace app\common\builder\table;
use app\common\builder\ListBuilder;

/**
 * 选项卡表格
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait TabsTrait
{
    /**
     * 是否选项表格
     * @var array
     * @author 贵州小白基地网络科技有限公司
     * @email 416716328@qq.com
     */
    protected $tabsConfig = [
        'active' => '',
        'field'  => '',
        'list'   => [],
    ];

    /**
     * 设置选项卡表格
     * @Author 贵州小白基地网络科技有限公司
     * @Email 416716328@qq.com
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