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

use plugin\xbCode\builder\Components\Form\InputKVS;

/**
 * 键值对象表单项
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait InputKvsRow
{
    /**
     * 添加键值对
     * @param string $field 字段名
     * @param string $title 标题
     * @param array $components 组件数组
     * @param mixed $value 默认值
     * @param callable|array $option
     * @return InputKVS
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowKeyValues(string $field, string $title, array $components, mixed $value = '', callable|array $option= [])
    {
        /** @var InputKVS */
        $component = $this->addRow(InputKVS::class, $field, $title, $value, $option);
        $component->valueItems($components);
        return $component;
    }
}
