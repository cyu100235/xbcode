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
namespace plugin\xbCode\builder\Renders\crud\column;

use plugin\xbCode\builder\Components\Date;
use plugin\xbCode\builder\Components\Table\TableColumn;

/**
 * 日期列
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait DateColumn
{
    /**
     * 添加日期列
     * @param string $name
     * @param string $label
     * @param callable|array $option
     * @return TableColumn|Date
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnDate(string $name, string $label, callable|array $option = [])
    {
        /** @var TableColumn|Date */
        $component = $this->useCustomColumn(Date::class,$name, $label, $option);
        return $component;
    }
}
