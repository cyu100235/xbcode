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

use plugin\xbCode\builder\Components\Form\Select;

/**
 * 下拉表单项
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait SelectRow
{    
    /**
     * 添加下拉选择组件
     * @param string $field
     * @param string $title
     * @param mixed $value
     * @param callable|array $option
     * @return Select
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowSelect(string $field, string $title, mixed $value = '', callable|array $option = [])
    {
        /** @var Select */
        $component = $this->addRow(Select::class, $field, $title, $value, $option);
        return $component;
    }
}
