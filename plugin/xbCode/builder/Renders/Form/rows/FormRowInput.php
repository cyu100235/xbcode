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

use plugin\xbCode\builder\Components\Form\InputText;
use plugin\xbCode\builder\Components\Form\InputGroup;

/**
 * 表单项-输入型
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait FormRowInput
{
    /**
     * 添加输入框
     * @param string $field
     * @param string $title
     * @param string $value
     * @param callable|array $option
     * @return InputText
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowInput(string $field, string $title, string $value = '', callable|array $option = []): InputText
    {
        /** @var InputText */
        $component = $this->addRow(InputText::class, $field, $title, $value, $option);
        return $component;
    }

    /**
     * 添加输入框组合
     * @param string $field
     * @param string $title
     * @param string $value
     * @param callable|array $option
     * @return InputGroup
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowInputGroup(string $field, string $title, string $value = '', callable|array $option = []): InputGroup
    {
        /** @var InputGroup */
        $component = $this->addRow(InputGroup::class, $field, $title, $value, $option);
        return $component;
    }
}
