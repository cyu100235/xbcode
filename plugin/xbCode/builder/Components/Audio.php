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
namespace plugin\xbCode\builder\Components;

/**
 * 音频渲染器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/audio
 * @method $this className(string $value) 外层 Dom 的类名
 * @method $this inline(bool $value) 是否是内联模式
 * @method $this src(string $value) 音频地址
 * @method $this loop(bool $value) 是否循环播放
 * @method $this autoPlay(bool $value) 是否自动播放
 * @method $this rates(array $value) 可配置音频播放倍速如：[1.0, 1.5, 2.0]
 * @method $this controls(array $value) 内部模块定制化
 */
class Audio extends BaseSchema
{
    public string $type = 'audio';

}
