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

use plugin\xbCode\builder\Components\Form\InputText;

/**
 * 模板表单项
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait TplRow
{
    /**
     * 添加模板组件
     * @param string $name 字段名
     * @param string $title 标题
     * @param string $value 参数值
     * @param callable|array $option 组件参数
     * @return InputText
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowTpl(string $name, string $title, string $value, callable|array $option = [])
    {
        /** @var InputText */
        $component = $this->addRow(InputText::class, $name, $title, $value, $option);
        $component->static(true);
        $component->staticSchema([
            'type' => 'tpl',
            'tpl' => $value,
        ]);
        return $component;
    }
}
