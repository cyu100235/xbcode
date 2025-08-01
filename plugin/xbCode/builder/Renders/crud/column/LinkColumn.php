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
namespace plugin\xbCode\builder\Renders\crud\column;

use plugin\xbCode\builder\Components\Action\LinkAction;

/**
 * 表格列组件
 * @copyright 贵州猿创科技有限公司
 * @author 楚羽幽 416716328@qq.com
 */
trait LinkColumn
{
    /**
     * 添加头像列
     * @param string $name
     * @param string $label
     * @param callable|array $option
     * @return LinkAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnLink(string $name, string $label, callable|array $option= [])
    {
        /** @var LinkAction */
        $component = $this->useCustomColumn(LinkAction::class,$name, $label, $option);
        $component->link('${' . $name . '}');
        return $component;
    }
}
