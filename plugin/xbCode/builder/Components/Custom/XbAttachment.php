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
namespace plugin\xbCode\builder\Components\Custom;

use plugin\xbCode\builder\Components\BaseSchema;

/**
 * 附件选择组件
 * @copyright 贵州猿创科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link http://www.xhadmin.cn
 * @method $this text(string $value) 文本占位
 * @method $this icon(string $value) 图标占位
 * @method $this limit(int $value) 最大上传数量
 * @method $this accept(string $value) 允许使用类型：image图片、audio音频、video视频、file其他文件，默认file
 */
class XbAttachment extends BaseSchema
{
    /**
     * 组件类型
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public string $type = 'xbAttachment';
}
