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
 * 视频组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/video
 * @method $this className(string $value) 外层 Dom 的类名
 * @method $this src(string $value) 视频地址
 * @method $this isLive(string $value) 是否为直播，视频为直播时需要添加上，支持flv和hls格式
 * @method $this videoType(string $value) 指定直播视频格式
 * @method $this poster(string $value) 视频封面地址
 * @method $this muted(string $value) 是否静音
 * @method $this loop(string $value) 是否循环播放
 * @method $this autoPlay(string $value) 是否自动播放
 * @method $this rates(string $value) 倍数，格式为[1.0, 1.5, 2.0]
 * @method $this frames(string $value) 时刻信息，key 是时刻信息，value 可以可以为空，可有设置为图片地址
 * @method $this jumpBufferDuration(string $value) 点击帧的时候默认是跳转到对应的时刻，如果想提前 3 秒钟，可以设置这个值为 3
 * @method $this stopOnNextFrame(string $value) 到了下一帧默认是接着播放，配置这个会自动停止
 * @method $this columnsCount(string $value) 帧的列数
 * @method $this framesClassName(string $value) 帧的类名
 * @method $this jumpFrame(string $value) 跳转帧
 * @method $this splitPoster(string $value) 分割海报
 * @method $this playerClassName(string $value) 播放器的类名
 * @method $this aspectRatio(string $value) 视频的宽高比
 */
class Video extends BaseSchema
{
    public string $type = 'video';
}
