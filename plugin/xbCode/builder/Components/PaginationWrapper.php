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
 * 分页容器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/pagination-wrapper
 * @method $this showPageInput(bool $value) 是否显示快速跳转输入框
 * @method $this maxButtons(int $value) 设置最大按钮数
 * @method $this inputName(string $value) 设置输入字段名
 * @method $this outputName(string $value) 设置输出字段名
 * @method $this perPage(int $value) 设置每页显示条数
 * @method $this position(string $value) 设置分页显示位置
 * @method $this body(array $value) 设置内容区域
 */
class PaginationWrapper extends BaseSchema
{
    public string $type = 'pagination-wrapper';
}
