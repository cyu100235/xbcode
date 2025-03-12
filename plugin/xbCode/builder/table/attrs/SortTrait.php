<?php

namespace plugin\xbCode\builder\table\attrs;

use plugin\xbCode\builder\ListBuilder;

/**
 * 排序配置
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait SortTrait
{
    /**
     * 排序配置
     * @Author 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-03-07
     * @param  array       $config
     * @return ListBuilder
     */
    public function sortConfig(array $config = []): ListBuilder
    {
        $this->sortConfig = array_merge([
            'trigger' => 'cell',
            'remote'  => true
        ], $config);
        return $this;
    }
}