<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @version  1.0
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Renders\Grid\Curd;

/**
 * 表格工具栏
 * @copyright 贵州猿创科技有限公司
 * @author 楚羽幽 416716328@qq.com
 */
trait GridToolbar
{
    use BulkAction;

    /**
     * CURD头部工具栏
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $gridHeaderToolbar = [];

    /**
     * 开启工具栏-表格列操作(点击下拉显示)
     * @param string $align
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function toolbarColumns(string $align = 'left')
    {
        $this->addToolbar($align, 'columns-toggler');
        return $this;
    }

    /**
     * 显示工具栏-表格列操作(点击弹窗显示)
     * @param string $align
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function toolbarColumnsDraggable(string $align = 'left')
    {
        $this->addToolbar($align, [
            "type" => "columns-toggler",
            "draggable" => true,
            "icon" => "fas fa-cog",
            "overlay" => true,
            "footerBtnSize" => "sm"
        ]);
        return $this;
    }

    /**
     * 开启工具栏-刷新按钮
     * @param string $align
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function toolbarReload(string $align = 'left')
    {
        $this->addToolbar($align, 'reload');
        return $this;
    }

    /**
     * 开启工具栏-分页工具
     * @param string $align
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function toolbarPage(string $align = 'right')
    {
        $this->useCRUD()->perPage(30);
        $this->addToolbar($align, 'pagination');
        return $this;
    }

    /**
     * 开启工具栏-拖拽排序
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function toolbarDrag(string $align = 'left')
    {
        $this->addToolbar($align, 'drag-toggler');
        return $this;
    }

    /**
     * 添加工具栏
     * @param string $align 对齐方式
     * @param string|array $value 工具栏名称
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function addToolbar(string $align, string|array $value)
    {
        // 检查是否已存在相同的工具栏
        $type = $value['type'] ?? $value;
        foreach ($this->gridHeaderToolbar as $toolbar) {
            if ($toolbar['type'] === $value) {
                return;
            }
        }
        // 添加新的工具栏
        if (is_array($value)) {
            $this->gridHeaderToolbar[] = array_merge([
                'align' => $align
            ], $value);
        } else {
            $this->gridHeaderToolbar[] = [
                'type' => $value,
                'align' => $align,
            ];
        }
    }
}
