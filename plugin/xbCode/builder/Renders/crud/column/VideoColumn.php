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

use plugin\xbCode\builder\Components\Video;
use plugin\xbCode\builder\Components\Table\TableColumn;

/**
 * 视频列
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait VideoColumn
{
    /**
     * 添加视频列
     * @param string $name
     * @param string $label
     * @param callable|array $option
     * @return TableColumn|Video
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnVideo(string $name, string $label, callable|array $option= [])
    {
        /** @var TableColumn|Video */
        $component = $this->useCustomColumn(Video::class, $name, $label, $option);
        return $component;
    }
}
