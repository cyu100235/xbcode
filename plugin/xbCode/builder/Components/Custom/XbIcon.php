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
 * 自定义图标组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link http://www.xbcode.net
 * @method $this icon(string $value) 图标名称
 * @method $this size(string $value) 图标大小
 * @method $this color(string $value) 图标颜色
 */
class XbIcon extends BaseSchema
{
    public string $type = 'xbIcons';

    /**
     * 设置表单图标展示
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function form()
    {
        $this->type = 'xbFormIcon';
        return $this;
    }
}
