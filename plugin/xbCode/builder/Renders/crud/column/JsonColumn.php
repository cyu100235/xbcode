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

use plugin\xbCode\builder\Components\Json;
use plugin\xbCode\builder\Components\Table\TableColumn;

/**
 * JSON列
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait JsonColumn
{
    /**
     * 添加JSON列
     * @param string $name
     * @param string $label
     * @param callable|array $option
     * @return TableColumn|Json
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnJson(string $name, string $label, callable|array $option = [])
    {
        /** @var TableColumn|Json */
        $component = $this->useCustomColumn(Json::class, $name, $label, $option);
        return $component;
    }
}
