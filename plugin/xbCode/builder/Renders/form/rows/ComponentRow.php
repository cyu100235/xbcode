<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Renders\form\rows;

use plugin\xbCode\builder\Components\Custom\Component;

/**
 * 自定义Vue组件表单项
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait ComponentRow
{
    /**
     * 添加远程Vue组件
     * @param string $field
     * @param string $title
     * @param mixed $value
     * @param string $url
     * @param callable|array $option
     * @return Component
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowVueRemote(string $field, string $title, mixed $value = '', string $url = '', callable|array $option = [])
    {
        if (empty($url)) {
            throw new \Exception('请设置远程组件的URL');
        }
        /** @var Component */
        $component = $this->addRow(Component::class, $field, $title, $value, $option);
        $component->url($url, [], [], 'xb-form-remote');
        return $component;
    }

    /**
     * 添加渲染Vue组件
     * @param string $field
     * @param string $title
     * @param mixed $value
     * @param string $template
     * @param callable|array $option
     * @return Component
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowVueRender(string $field, string $title, mixed $value = '', string $template = '', callable|array $option = [])
    {
        if (empty($template)) {
            throw new \Exception('请设置渲染组件模板');
        }
        /** @var Component */
        $component = $this->addRow(Component::class, $field, $title, $value, $option);
        $component->body($template, [], [], 'xb-form-render');
        return $component;
    }

    /**
     * 添加Vue组件
     * @param string $field
     * @param string $title
     * @param mixed $value
     * @param string $path
     * @param callable|array $option
     * @throws \Exception
     * @return Component
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowVue(string $field, string $title, mixed $value = '', string $path = '', callable|array $option = [])
    {
        if (empty($path)) {
            throw new \Exception('请设置组件模板路径');
        }
        if (!file_exists($path)) {
            throw new \Exception('组件模板路径不存在');
        }
        $template = file_get_contents($path);
        $component = $this->addRowVueRender($field, $title, $value, $template, $option);
        return $component;
    }
}
