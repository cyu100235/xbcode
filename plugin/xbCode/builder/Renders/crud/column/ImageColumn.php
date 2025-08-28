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

use plugin\xbCode\builder\Components\Image;
use plugin\xbCode\builder\Components\Table\TableColumn;

/**
 * 图片列
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait ImageColumn
{
    /**
     * 添加图片列
     * @param string $name
     * @param string $label
     * @param callable|array $option
     * @return TableColumn|Image
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnImage(string $name, string $label, callable|array $option = [])
    {
        /** @var TableColumn|Image */
        $component = $this->useCustomColumn(Image::class, $name, $label, $option);
        $component->width(30);
        $component->height(30);
        $component->align('center');
        $component->vAlign('middle');
        return $component;
    }
}
