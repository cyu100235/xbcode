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

use plugin\xbCode\builder\Components\Custom\XbVideo;

/**
 * 视频组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait VideoRow
{
    /**
     * 添加视频组件
     * @param string $field
     * @param string $title
     * @param mixed $value
     * @param callable|array $config
     * @return XbVideo
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowVideo(string $field, string $title, mixed $value = '', callable|array $option= [])
    {
        /** @var XbVideo */
        $component = $this->addRow(XbVideo::class, $field, $title, $value, $option);
        return $component;
    }
}
