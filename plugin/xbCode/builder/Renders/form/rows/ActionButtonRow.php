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

use plugin\xbCode\builder\Components\Action\AjaxAction;

/**
 * 按钮表单项
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait ActionButtonRow
{
    /**
     * 添加ajax行为按钮操作
     * @param string $label 按钮文本
     * @param string|array $api 接口请求地址
     * @param callable|array $option 组件参数
     * @return AjaxAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function addRowButtonAction(string $label, string|array $api, callable|array $option = [])
    {
        /** @var AjaxAction */
        $component = $this->addRow(AjaxAction::class, '', '');
        $component->label($label);
        $component->actionType('ajax');
        $component->level('primary');
        $component->className('mt-5 mb-5');
        $component->api($api);
        if (is_array($option)) {
            $component->setVariables($option);
        } else if (is_callable($option)) {
            $option($component);
        }
        return $component;
    }
}
