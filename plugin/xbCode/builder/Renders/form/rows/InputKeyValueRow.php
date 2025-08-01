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

use plugin\xbCode\builder\Components\Form\InputKV;

/**
 * 键值对表单项
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait InputKeyValueRow
{
    /**
     * 添加键值对
     * @param string $field
     * @param string $title
     * @param mixed $value
     * @param callable|array $option
     * @return InputKV
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowKeyValue(string $field, string $title, mixed $value = '', callable|array $option= [])
    {
        /** @var InputKV */
        $component = $this->addRow(InputKV::class, $field, $title, $value, $option);
        return $component;
    }
}
