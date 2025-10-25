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
namespace plugin\xbCode\builder\Renders\crud\layouts;

/**
 * 头部工具栏布局
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait ToolbarLayout
{
    use HeaderToolbar;
    use FooterToolbarLayout;

    /**
     * 头部工具栏组件
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $headerToolbar = [];

    /**
     * 添加刷新工具按钮
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addToolbarReload()
    {
        // 检测是否已存在刷新按钮
        $state = array_filter($this->headerToolbar, function ($item) {
            return $item['type'] === 'button' && $item['actionType'] === 'reload';
        });
        if(empty($state)) {
            $this->headerToolbar[] = [
                'type' => 'button',
                'actionType' => 'reload',
                'icon' => 'fa fa-repeat',
                'target' => 'crud',
                'align' => 'right',
            ];
        }
        return $this;
    }

    /**
     * 添加头部列配置按钮
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addToolbarColumnsTogglable()
    {
        // 检测是否已存在刷新按钮
        $state = array_filter($this->headerToolbar, function ($item) {
            return $item['type'] === 'columns-toggler';
        });
        if(empty($state)){
            $this->headerToolbar[] = [
                'type' => 'columns-toggler',
                'align' => 'right',
            ];
        }
        return $this;
    }

    /**
     * 获取头部工具栏组件
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getHeaderToolbar()
    {
        return $this->headerToolbar;
    }
}
