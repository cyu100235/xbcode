<?php

namespace xbcode\builder\table\attrs;
use xbcode\builder\ListBuilder;

/**
 * 表格行配置
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait RowTrait
{
    // 行配置
    private $rowConfig = [
        'keyField' => 'id',
        'isHover'  => true,
        'height' => 'auto',
    ];
    
    /**
     * 行配置
     * @Author 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-03-07
     * @param  array       $config
     * @return ListBuilder
     */
    public function rowConfig(array $config = []): ListBuilder
    {
        $this->rowConfig = array_merge($this->rowConfig, $config);
        return $this;
    }
}