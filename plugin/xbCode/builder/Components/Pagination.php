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
 * 分页组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/pagination
 * @method $this mode(string $value) 设置模式
 * @method $this layout(string|array $value) 设置布局
 * @method $this maxButtons(int $value) 设置最大按钮数
 * @method $this total(string|int $value) 设置总条数
 * @method $this activePage(string|int $value) 设置当前页数
 * @method $this perPage(string|int $value) 设置每页显示条数
 * @method $this showPerPage(bool $value) 是否展示每页切换器
 * @method $this size(string $value) 设置组件尺寸
 * @method $this ellipsisPageGap(string|int $value) 设置多页跳转页数
 * @method $this perPageAvailable(array $value) 设置每页可用条数
 * @method $this showPageInput(bool $value) 是否显示快速跳转输入框
 * @method $this disabled(bool $value) 是否禁用
 * @method $this onPageChange(callable $value) 分页改变触发
 * @method $this className(string $value) 设置组件类名
 */
class Pagination extends BaseSchema
{
    public string $type = 'pagination';
}
