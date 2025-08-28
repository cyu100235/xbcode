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
namespace plugin\xbCode\builder\Renders\form\rows;

use plugin\xbCode\builder\Components\Custom\XbIcon;

/**
 * 图标展示表单项
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait IconRow
{
    /**
     * 添加图标展示
     * @param string $field
     * @param string $title
     * @param mixed $value
     * @param callable|array $option
     * @return XbIcon
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowIcon(string $field, string $title, mixed $value = '', callable|array $option= [])
    {
        /** @var XbIcon */
        $component = $this->addRow(XbIcon::class, $field, $title, $value, $option);
        $component->form();
        return $component;
    }
}
