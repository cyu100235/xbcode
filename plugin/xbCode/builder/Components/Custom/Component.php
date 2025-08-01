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
namespace plugin\xbCode\builder\Components\Custom;

use plugin\xbCode\builder\Components\BaseSchema;

/**
 * 远程渲染Vue组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class Component extends BaseSchema
{
    /**
     * 组件类型
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public string $type = 'xbRender';

    /**
     * 远程组件接口
     * @param string $url 组件接口地址
     * @param array $vars 附带变量
     * @param array $option 属性设置
     * @param string $type 组件类型
     * @return static
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     */
    public function url(string $url, array $vars = [], array $option = [], string $type = 'xbRemote')
    {
        $this->type = $type;
        $this->setVariable('url', $url);
        $this->setVariable('vars', $vars);
        $this->setVariables($option);
        return $this;
    }

    /**
     * 渲染组件内容
     * @param string $component 组件内容
     * @param array $vars 附带变量
     * @param array $option 属性设置
     * @param string $type 组件类型
     * @return static
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     */
    public function body(string $component, array $vars = [], array $option = [], string $type = 'xbRender')
    {
        $this->type = $type;
        $this->setVariable('body', $component);
        $this->setVariable('vars', $vars);
        $this->setVariables($option);
        return $this;
    }
}
