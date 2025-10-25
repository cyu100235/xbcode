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

use plugin\xbCode\builder\Components\Divider;

/**
 * 分割线表单项
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait DividerRow
{
    /**
     * 添加分割线组件
     * @param string $title 分割线标题，可为空
     * @return Divider
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowDivider(string $title)
    {
        /** @var Divider */
        $component = $this->addRow(Divider::class, '', '');
        $component->title($title);
        return $component;
    }
}
