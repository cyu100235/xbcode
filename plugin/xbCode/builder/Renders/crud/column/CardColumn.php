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

use plugin\xbCode\builder\Components\Card;

/**
 * 表格列组件
 * @copyright 贵州猿创科技有限公司
 * @author 楚羽幽 416716328@qq.com
 */
trait CardColumn
{
    /**
     * 添加卡片列
     * @param string $name
     * @param string $label
     * @param array $fields
     * @param callable|array $option
     * @return Card
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnCard(string $name, string $label, array $fields = [], callable|array $option= [])
    {
        /** @var Card */
        $component = $this->useCustomColumn(Card::class,$name, $label, $option);
        // 设置卡片属性
        $title = $fields['title'] ?? 'title';
        $subTitle = $fields['subtitle'] ?? 'subtitle';
        $image = $fields['image'] ?? 'image';
        $component->header([
            'title' => "<%= this.{$title} %>",
            'subTitle' => "<%= this.{$subTitle} %>",
            'avatar' => "<%= this.{$image} %>",
            'avatarClassName' => 'pull-left thumb-md avatar b-3x m-r',
        ]);
        return $component;
    }
}
