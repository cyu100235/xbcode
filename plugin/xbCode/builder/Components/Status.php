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
 * 状态组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/status
 * @method $this className(string $value) 外层 Dom 的 CSS 类名
 * @method $this placeholder(string $value) 占位文本
 * @method $this map(array $value) 映射图标
 * @method $this labelMap(array $value) 映射文本
 * @method $this source(array $value) 自定义映射状态，支持数据映射
 */
class Status extends BaseSchema
{
    public string $type = 'status';
}
