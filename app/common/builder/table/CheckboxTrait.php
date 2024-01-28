<?php

namespace app\common\builder\table;
use app\common\builder\ListBuilder;

/**
 * 筛选查询
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait CheckboxTrait
{
    /**
     * 复选框配置
     *
     * @Author 贵州小白基地网络科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @param  array       $config
     * @return ListBuilder
     */
    public function checkboxConfig(array $config = []): ListBuilder
    {
        $this->checkboxConfig = array_merge([
            'labelField' => 'id',
            'reserve'    => true,
            'highlight'  => true,
            'range'      => true
        ], $config);
        return $this;
    }
}