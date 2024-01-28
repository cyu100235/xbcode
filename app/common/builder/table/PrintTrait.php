<?php

namespace app\common\builder\table;
use app\common\builder\ListBuilder;

/**
 * 打印配置
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait PrintTrait
{
    /**
     * 打印配置
     * @Author 贵州小白基地网络科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @param  array       $config
     * @return ListBuilder
     */
    public function printConfig(array $config = []): ListBuilder
    {
        $this->printConfig = array_merge([], $config);
        return $this;
    }
}