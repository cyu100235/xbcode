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
namespace plugin\xbCode\builder\Renders\form\rows;

use plugin\xbCode\builder\Components\Alert;

/**
 * Alert提示表单项
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait AlertRow
{
    /**
     * 添加Alert提示
     * @param mixed $content 显示内容
     * @param string $type 级别类型：info、success、warning、danger
     * @param string $title 提示标题
     * @param callable|array $option 组件参数
     * @return Alert
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowAlert(mixed $content = '', string $type = 'info', string $title = '温馨提示', callable|array $option = [])
    {
        /** @var Alert */
        $component = $this->addRow(Alert::class, '', '');
        $component->showIcon(true);
        if ($title) {
            $component->title($title);
        }
        if ($content) {
            $component->body($content);
        }
        if ($type) {
            $component->level($type);
        }
        if (is_array($option)) {
            $component->setVariables($option);
        } else if (is_callable($option)) {
            $option($component);
        }
        return $component;
    }
}
