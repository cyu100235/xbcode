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
 * 矩阵勾选组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/matrix-checkboxes
 * @method $this columns(array $columns) 列信息
 * @method $this rows(array $rows) 行信息
 * @method $this rowLabel(string $rowLabel) 行标题说明
 * @method $this source(string $source) 设置数据源API地址
 * @method $this multiple(bool $multiple) 是否多选
 * @method $this singleSelectMode(string $singleSelectMode) 单选模式
 * @method $this textAlign(string $textAlign) 文本对齐方式
 * @method $this yCheckAll(bool $yCheckAll) 列上的全选
 * @method $this xCheckAll(bool $xCheckAll) 行上的全选
 */
class MatrixCheckboxes extends FormBase
{
    public string $type = 'matrix-checkboxes';
}
