<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Renders\form\rows;

use plugin\xbCode\builder\Components\Form\Hidden;

/**
 * 隐藏表单项
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait HiddenRow
{
    /**
     * 添加隐藏表单项
     * @param string $field
     * @param mixed $value
     * @param callable|array $option
     * @return Hidden
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowHidden(string $field, mixed $value = '', callable|array $option= [])
    {
        /** @var Hidden */
        $component = $this->addRow(Hidden::class, $field, '', $value, $option);
        return $component;
    }
}
