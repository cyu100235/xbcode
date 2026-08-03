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
namespace plugin\xbCode\builder\Renders\crud\column;

use plugin\xbCode\builder\Components\Custom\Component;
use plugin\xbCode\builder\Components\Table\TableColumn;

/**
 * Vue单元组件渲染
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait VueComponentColumn
{
    /**
     * Vue远程渲染组件
     * @param string $name 列字段
     * @param string $label 列名称
     * @param string $url 远程地址
     * @param callable|array $option
     * @return TableColumn|Component
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnVueRemote(string $name, string $label, string $url = '', callable|array $option = [])
    {
        if (empty($url)) {
            throw new \Exception('请设置远程组件的URL');
        }
        /** @var TableColumn|Component */
        $component = $this->useCustomColumn(Component::class, $name, $label, $option);
        $component->url($url, [], [], 'XbRemote');
        return $component;
    }

    /**
     * Vue组件渲染
     * @param string $name 列字段
     * @param string $label 列名称
     * @param string $template 组件模板
     * @param callable|array $option
     * @return Component|TableColumn
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnVueRender(string $name, string $label, string $template = '', callable|array $option = [])
    {
        if (empty($template)) {
            throw new \Exception('请设置渲染组件模板');
        }
        /** @var TableColumn|Component */
        $component = $this->useCustomColumn(Component::class, $name, $label, $option);
        $component->body($template, [], [], 'XbRender');
        return $component;
    }

    /**
     * Vue路径模板渲染
     * @param string $name 列字段
     * @param string $label 列名称
     * @param string $path 组件模板路径
     * @param callable|array $option
     * @return Component|TableColumn
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnVue(string $name, string $label, string $path = '', callable|array $option = [])
    {
        if (empty($path)) {
            throw new \Exception('请设置组件模板路径');
        }
        if (!file_exists($path)) {
            throw new \Exception("组件不存在：{$path}");
        }
        $template = file_get_contents($path);
        /** @var TableColumn|Component */
        $component = $this->addColumnVueRender($name, $label, $template, $option);
        return $component;
    }
}
