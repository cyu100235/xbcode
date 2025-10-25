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

use plugin\xbCode\builder\Components\Form\AmisSwitch;
use plugin\xbCode\builder\Components\Table\TableColumn;

/**
 * 开关列
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait SwitchColumn
{
    /**
     * 添加开关列
     * @param string $name 字段名称
     * @param string $label 标签名称
     * @param array $quickEdit 保存API配置
     * - `onText` 开启时文本
     * - `offText` 关闭时文本
     * - `falseValue` 关闭时值
     * - `trueValue` 开启时值
     * - `mode` 显示模式，默认为`inline`
     * - `saveImmediately` 是否立即保存，默认为`true`
     * @param callable|array $option
     * @throws \Exception
     * @return TableColumn|AmisSwitch
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnSwitch(string $name, string $label, array $quickEdit = [], callable|array $option = [])
    {
        if (empty($this->useCRUD()->quickSaveItemApi)) {
            throw new \Exception('请先设置【quickSaveItemApi】接口地址');
        }
        /** @var TableColumn|AmisSwitch */
        $component = $this->useCustomColumn(AmisSwitch::class, $name, $label, $option);
        $component->width(120);
        $component->actionType('ajax');
        $component->align('center');
        $component->vAlign('middle');
        $component->quickEdit([
            'type' => 'switch',
            'mode' => 'inline',
            'saveImmediately' => true,
            ...$quickEdit,
        ]);
        return $component;
    }
}
