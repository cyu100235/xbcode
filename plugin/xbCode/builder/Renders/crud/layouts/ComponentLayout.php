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

use plugin\xbCode\builder\Components\Custom\Component;

/**
 * 自定义布局
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait ComponentLayout
{
    /**
     * 自定义组件列表
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $components = [];
    
    /**
     * 添加头部自定义组件
     * @param string $url
     * @param array $vars
     * @param array $option
     * @return Component
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addHeaderComponent(string $url, array $vars = [], array $option = [])
    {
        $component = new Component;
        $component->className('xb-header-component');
        $component->url($url, $vars, $option);
        $this->components[] = $component;
        return $component;
    }

    /**
     * 获取自定义组件列表
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getComponents()
    {
        return $this->components;
    }
}
