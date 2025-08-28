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

use plugin\xbCode\builder\Components\Custom\XbVditor;

/**
 * vditor编辑器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait VditorRow
{
    /**
     * 添加vditor编辑器
     * @param string $field
     * @param string $title
     * @param mixed $value
     * @param callable|array $config
     * @return XbVditor
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowEditorVditor(string $field, string $title, mixed $value = '', callable|array $option= [])
    {
        /** @var XbVditor */
        $component = $this->addRow(XbVditor::class, $field, $title, $value, $option);
        $component->options([
            'height' => 500,
        ]);
        return $component;
    }
}
