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
 * 搜索框组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/search-box
 * @method $this className(string $value) 外层 CSS 类名
 * @method $this mini(bool $value) 是否为 mini 模式
 * @method $this searchImediately(bool $value) 是否立即搜索
 * @method $this clearAndSubmit(bool $value) 清空搜索框内容后立即执行搜索
 * @method $this disabled(bool $value) 是否为禁用状态
 * @method $this loading(bool $value) 是否处于加载状态
 */
class SearchBox extends BaseSchema
{
    public string $type = 'search-box';
}
