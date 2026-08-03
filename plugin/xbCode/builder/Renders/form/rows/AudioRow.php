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

use plugin\xbCode\builder\Components\Custom\XbAudio;

/**
 * 音频组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait AudioRow
{
    /**
     * 添加音频组件
     * @param string $field
     * @param string $title
     * @param mixed $value
     * @param callable|array $option
     * @return XbAudio
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowAudio(string $field, string $title, mixed $value = '', callable|array $option= [])
    {
        /** @var XbAudio */
        $component = $this->addRow(XbAudio::class, $field, $title, $value, $option);
        return $component;
    }
}
