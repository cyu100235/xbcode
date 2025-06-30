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
namespace plugin\xbCode\builder\Renders\Form\rows;

use plugin\xbCode\builder\Components\Form\Radios;
use plugin\xbCode\builder\Components\Form\Select;
use plugin\xbCode\builder\Components\Form\InputCity;
use plugin\xbCode\builder\Components\Form\Checkboxes;
use plugin\xbCode\builder\Components\Form\InputSwitch;

/**
 * 表单项-选项型
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait FormRowOptions
{
    /**
     * 添加链式下拉组件
     * @param string $field
     * @param string $title
     * @param string $value
     * @param callable|array $option
     * @return InputSwitch
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowChainedSelect(string $field, string $title, string $value = '', callable|array $option = [])
    {
        /** @var InputSwitch */
        $component = $this->addRow(InputSwitch::class, $field, $title, $value, $option);
        return $component;
    }
    
    /**
     * 添加下拉选择组件
     * @param string $field
     * @param string $title
     * @param string $value
     * @param callable|array $option
     * @return Select
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowSelect(string $field, string $title, string $value = '', callable|array $option = [])
    {
        /** @var Select */
        $component = $this->addRow(Select::class, $field, $title, $value, $option);
        return $component;
    }

    /**
     * 添加单选框组件
     * @param string $field
     * @param string $title
     * @param string $value
     * @param callable|array $option
     * @return Radios
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowRadio(string $field, string $title, string $value = '', callable|array $option = [])
    {
        /** @var Radios */
        $component = $this->addRow(Radios::class, $field, $title, $value, $option);
        return $component;
    }

    /**
     * 添加复选框组件
     * @param string $field
     * @param string $title
     * @param string $value
     * @param callable|array $option
     * @return Checkboxes
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowCheckbox(string $field, string $title, string $value = '', callable|array $option = [])
    {
        /** @var Checkboxes */
        $component = $this->addRow(Checkboxes::class, $field, $title, $value, $option);
        return $component;
    }
    

    /**
     * 添加城市选择组件
     * @param string $field
     * @param string $title
     * @param string $value
     * @param callable|array $option
     * @return InputCity
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowCity(string $field, string $title, string $value = '', callable|array $option = [])
    {
        /** @var InputCity */
        $component = $this->addRow(InputCity::class, $field, $title, $value, $option);
        return $component;
    }
}
