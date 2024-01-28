<?php

namespace app\common\builder\table;
use app\common\builder\ListBuilder;

/**
 * 自定义配置
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait EditColumnTrait
{
    /**
     * 开启编辑
     * @Author 贵州小白基地网络科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-13
     * @param  array       $config
     * @return ListBuilder
     */
    public function editConfig(array $config = []): ListBuilder
    {
        $this->editConfig = array_merge([
            'enabled'    => true,
            'trigger'    => 'dblclick',
            'mode'       => 'cell',
            'showStatus' => true
        ], $config);
        return $this;
    }
}