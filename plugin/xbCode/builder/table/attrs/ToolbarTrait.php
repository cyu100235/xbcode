<?php

namespace plugin\xbCode\builder\table\attrs;
use plugin\xbCode\builder\ListBuilder;

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
     * 设置刷新配置
     * @param bool $state
     * @return \app\common\builder\ListBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function setRefresh(bool $state = true): ListBuilder
    {
        $this->toolbarConfig['refresh'] = $state;
        return $this;
    }

    /**
     * 设置导入配置
     * @param bool $state
     * @return \app\common\builder\ListBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function setImport(bool $state = true): ListBuilder
    {
        $this->toolbarConfig['import'] = $state;
        return $this;
    }

    /**
     * 设置导出配置
     * @param bool $state
     * @return \app\common\builder\ListBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function setExport(bool $state = true): ListBuilder
    {
        $this->toolbarConfig['export'] = $state;
        return $this;
    }

    /**
     * 设置打印配置
     * @param bool $state
     * @return \app\common\builder\ListBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function setPrint(bool $state = true): ListBuilder
    {
        $this->toolbarConfig['print'] = $state;
        return $this;
    }

    /**
     * 设置全屏缩放配置
     * @param bool $state
     * @return \app\common\builder\ListBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function setZoom(bool $state = true): ListBuilder
    {
        $this->toolbarConfig['zoom'] = $state;
        return $this;
    }

    /**
     * 设置自定义表格列配置
     * @param bool $state
     * @return \app\common\builder\ListBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function setCustom(bool $state = true): ListBuilder
    {
        $this->toolbarConfig['custom'] = $state;
        return $this;
    }
}