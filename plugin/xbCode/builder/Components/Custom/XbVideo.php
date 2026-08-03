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
namespace plugin\xbCode\builder\Components\Custom;

use plugin\xbCode\builder\Components\BaseSchema;

/**
 * 自定义视频组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link http://www.xbcode.net
 * @method $this title(string $value) 标题
 * @method $this url(string $value) 视频地址
 */
class XbVideo extends BaseSchema
{
    public string $type = 'xb-video';
}
