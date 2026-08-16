<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Renders\form\layouts;

use Exception;

/**
 * 侧边栏表单布局能力
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait SidebarLayout
{
    /**
     * 侧边栏表单位置（left=左边，right=右边）
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $sidebarPosition = 'left';

    /**
     * 副侧边栏表单组件
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $sidebarForm = [];

    /**
     * 添加副侧边栏组件
     * @param array $components
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addSidebarForm(array $components)
    {
        $this->sidebarForm = $components;
        return $this;
    }

    /**
     * 设置侧边栏表单位置
     * @param string $value left=左侧,right=右侧
     * @throws Exception
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function sidebarPosition(string $value = 'left')
    {
        if(!in_array($value, ['left', 'right'])){
            throw new Exception('侧边栏表单位置参数错误');
        }
        $this->sidebarPosition = $value;
        return $this;
    }
}