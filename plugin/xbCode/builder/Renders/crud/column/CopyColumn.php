<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Renders\crud\column;

use plugin\xbCode\builder\Components\Action\UrlAction;
use plugin\xbCode\builder\Components\Table\TableColumn;

/**
 * 复制列
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait CopyColumn
{
    /**
     * 添加复制列
     * @param string $name
     * @param string $label
     * @param callable|array $option
     * @return TableColumn
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnCopy(string $name, string $label, callable|array $option = [])
    {
        $component = $this->useCustomColumn(UrlAction::class, $name, $label, $option);
        $component->align('center');
        $component->vAlign('middle');
        $component->level('link');
        $component->actionType('copy');
        $component->content('${' . $name . '}');
        $title = $option['title'] ?? '点击复制';
        $html = <<<HTML
        <span class="text-primary cursor-pointer" style="font-size: 12px;">$title</span>
        HTML;
        $component->body([
            [
                'type' => 'tpl',
                'tpl' => $html,
            ],
        ]);
        return $component;
    }
}
