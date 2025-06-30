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
namespace plugin\xbCode\builder\Renders\Grid;

use plugin\xbCode\builder\Components\Form\InputTree;

/**
 * 表格侧边栏
 * @copyright 贵州猿创科技有限公司
 * @author 楚羽幽 416716328@qq.com
 */
trait GridSidebar
{
    /**
     * 侧边栏组件
     * @var InputTree
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected InputTree $inputTree;
    
    /**
     * 添加侧边栏选项
     * @param array $sideBar
     * @throws \InvalidArgumentException
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addSidebar(array $sideBar)
    {
        if(empty($sideBar)) {
            throw new \InvalidArgumentException('侧边栏选项数据不能为空');
        }
        $this->inputTree->options($sideBar);
        return $this;
    }
    
    /**
     * 渲染侧边栏
     * @return InputTree
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function renderSidebar()
    {
        return $this->inputTree;
    }
}
