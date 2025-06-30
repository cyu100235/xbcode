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

use plugin\xbCode\builder\Components\Form\Checkboxes;

/**
 * 表单项-日期时间型
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait FormRowDate
{
    /**
     * 添加日期组件
     * @param string $field
     * @param string $title
     * @param string $value
     * @param callable|array $option
     * @return Checkboxes
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowDate(string $field, string $title, string $value = '', callable|array $option = [])
    {
        /** @var Checkboxes */
        $component = $this->addRow(Checkboxes::class, $field, $title, $value, $option);
        return $component;
    }

    /**
     * 添加日期时间组件
     * @param string $field
     * @param string $title
     * @param string $value
     * @param callable|array $option
     * @return Checkboxes
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowDateTime(string $field, string $title, string $value = '', callable|array $option = [])
    {
        /** @var Checkboxes */
        $component = $this->addRow(Checkboxes::class, $field, $title, $value, $option);
        return $component;
    }
}
