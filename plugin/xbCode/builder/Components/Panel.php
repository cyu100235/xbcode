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
 * 面板组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/panel
 * @method $this className(string $value) 设置组件类名
 * @method $this headerClassName(string $value) 设置头部区域类名
 * @method $this footerClassName(string $value) 设置底部区域类名
 * @method $this actionsClassName(string $value) 设置操作区域类名
 * @method $this bodyClassName(string $value) 设置内容区域类名
 * @method $this title(string|array $value) 设置标题
 * @method $this header(string|array $value) 设置头部容器
 * @method $this body(string|array $value) 设置内容容器
 * @method $this footer(string|array $value) 设置底部容器
 * @method $this affixFooter(bool $value) 是否固定底部容器
 * @method $this actions(array $value) 设置操作区域
 */
class Panel extends BaseSchema
{
    public string $type = 'panel';
}
