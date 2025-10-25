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

use plugin\xbCode\builder\Components\AmisHtml;
use plugin\xbCode\builder\Components\Form\InputText;

/**
 * HTML表单项
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait HtmlRow
{
    /**
     * 添加HTML
     * @param string $field
     * @param string $title
     * @param mixed $value
     * @param callable|array $option
     * @return AmisHtml
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowHtml(string $html)
    {
        /** @var AmisHtml */
        $component = $this->addRow(AmisHtml::class, '', '');
        $component->html($html);
        return $component;
    }
}
