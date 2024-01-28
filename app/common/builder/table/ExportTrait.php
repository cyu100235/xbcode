<?php

namespace app\common\builder\table;
use app\common\builder\ListBuilder;

/**
 * 筛选查询
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait ExportTrait
{
    /**
     * 导出配置
     * @Author 贵州小白基地网络科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @param  array       $config
     * @return ListBuilder
     */
    public function exportConfig(array $config = []): ListBuilder
    {
        // 开启导出按钮
        $this->toolbarConfig([
            'export' => true
        ]);
        // 导出配置参数
        $this->exportConfig = array_merge([
            'api'    => '',
            'remote' => true,
            'types'  => ['xlsx', 'xls'],
            'modes'  => ['current', 'selected', 'all'],
        ], $config);
        return $this;
    }
}