<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Renders\crud\layouts;

use plugin\xbCode\builder\Components\Tpl;
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
     * 添加头部URL组件
     * @param string $url
     * @param array $vars
     * @param array $option
     * @return Component
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addHeaderUrlComponent(string $url, array $vars = [], array $option = [])
    {
        $component = new Component;
        $component->className('xb-header-component');
        $component->url($url, $vars, $option);
        $this->components[] = $component;
        return $component;
    }
    
    /**
     * 添加头部Vue组件
     * @param string $path 组件路径
     * @param array $vars 透传参数
     * @param array $option 参数选项
     * @throws \Exception
     * @return Component
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    public function addHeaderRenderVue(string $path, array $vars = [], array $option = [])
    {
        if (empty($path)) {
            throw new \Exception('请设置组件模板路径');
        }
        if (!file_exists($path)) {
            throw new \Exception("组件不存在：{$path}");
        }
        $template = file_get_contents($path);
        $component = $this->addHeaderRenderComponent($template, $vars, $option);
        return $component;
    }

    /**
     * 添加头部渲染组件
     * @param string $template
     * @param array $vars
     * @param array $option
     * @return Component
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    public function addHeaderRenderComponent(string $template, array $vars = [], array $option = [])
    {
        $component = new Component;
        $component->className('xb-header-component');
        $component->body($template, $vars, $option);
        $this->components[] = $component;
        return $component;
    }

    /**
     * 渲染HTML模板组件
     * @param string $html HTML模板
     * @param array $option 参数设置
     * @return Tpl
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    public function addHeaderRenderHtml(string $html, array $option = [])
    {
        $component = new Tpl;
        $component->tpl($html);
        if ($option) {
            $component->setVariables($option);
        }
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
