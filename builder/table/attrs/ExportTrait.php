<?php

namespace plugin\xbCode\builder\table\attrs;
use plugin\xbCode\builder\ListBuilder;

/**
 * 导出配置
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait ExportTrait
{
    /**
     * 导出配置
     * @Author 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-03-07
     * @param  array       $config
     * @return ListBuilder
     */
    public function exportConfig(array $config = []): ListBuilder
    {
        // 开启导出按钮
        $this->setExport();
        // 导出配置参数
        $this->exportConfig = array_merge([
            // 导出API接口
            'api' => '',
            // 是否服务端导出
            'is_server' => true,
            // 文件类型
            'type' => 'xlsx',
            // 可选文件类型列表
            'types' => ['xlsx', 'xls'],
            // 导出模式 current, selected, all
            'modes' => ['current', 'selected', 'all'],
            // 导出文件名
            'filename' => '文件名称',
            // 导出表格名称
            'sheetName' => '',
        ], $config);
        return $this;
    }
}