<?php

namespace app\common\builder\table;
use app\common\builder\ListBuilder;

/**
 * 工具栏配置
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait ToolbarTrait
{
    // 工具栏配置
    protected $toolbarConfig = [
        // 是否开启刷新按钮
        'refresh' => true,
        // 是否开启导入按钮
        'import'  => false,
        // 是否开启导出按钮
        'export'  => false,
        // 是否开启打印按钮
        'print'   => false,
        // 是否开启全屏缩放按钮
        'zoom'    => true,
        // 是否开启自定义表格列
        'custom'  => true
    ];

    /**
     * 工具栏配置
     *
     * @Author 贵州小白基地网络科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @param  array       $toolbar
     * @return ListBuilder
     */
    public function toolbarConfig(array $toolbar = []): ListBuilder
    {
        $toolbar             = array_merge($this->toolbarConfig, $toolbar);
        $this->toolbarConfig = $toolbar;
        return $this;
    }
}