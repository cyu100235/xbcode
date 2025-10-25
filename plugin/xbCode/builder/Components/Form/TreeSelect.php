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
namespace plugin\xbCode\builder\Components\Form;

/**
 * 树型选择框
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/treeselect
 * @method $this hideNodePathLabel(bool $value) 设置是否隐藏选择框中已选择节点的路径 label 信息
 * @method $this onlyLeaf(bool $value) 设置是否只允许选择叶子节点
 * @method $this searchable(bool $value) 设置是否可检索，仅在 type 为 tree-select 的时候生效
 */
class TreeSelect extends InputTree
{
    public string $type = 'tree-select';
}
