<?php

namespace xbcode\builder\table\attrs;
use xbcode\builder\ListBuilder;

/**
 * 复选框配置
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait CheckboxTrait
{
    /**
     * 复选框配置
     * @param array $config
     * @return \app\common\builder\ListBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
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