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
 * 表单表格组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-table
 * @method $this addable(bool $value) 设置是否可增加一行
 * @method $this copyable(bool $value) 设置是否可复制一行
 * @method $this copyData(array $value) 设置控制复制时的数据映射，不配置时复制整行数据
 * @method $this childrenAddable(bool $value) 设置是否可增加子级节点
 * @method $this editable(bool $value) 设置是否可编辑
 * @method $this removable(bool $value) 设置是否可删除
 * @method $this showTableAddBtn(bool $value) 设置是否显示表格操作栏添加按钮，前提是要开启可新增功能
 * @method $this showFooterAddBtn(bool $value) 设置是否显示表格下方添加按，前提是要开启可新增功能
 * @method $this addApi(string|ApiSchema $value) 设置新增时提交的 API
 * @method $this footerAddBtn(SchemaNode $value) 设置底部新增按钮配置
 * @method $this updateApi(string|ApiSchema $value) 设置修改时提交的 API
 * @method $this deleteApi(string|ApiSchema $value) 设置删除时提交的 API
 * @method $this addBtnLabel(string $value) 设置增加按钮名称
 * @method $this addBtnIcon(string $value) 设置增加按钮图标
 * @method $this subAddBtnLabel(string $value) 设置子级增加按钮名称
 * @method $this subAddBtnIcon(string $value) 设置子级增加按钮图标
 * @method $this copyBtnLabel(string $value) 设置复制按钮文字
 * @method $this copyBtnIcon(string $value) 设置复制按钮图标
 * @method $this editBtnLabel(string $value) 设置编辑按钮名称
 * @method $this editBtnIcon(string $value) 设置编辑按钮图标
 * @method $this deleteBtnLabel(string $value) 设置删除按钮名称
 * @method $this deleteBtnIcon(string $value) 设置删除按钮图标
 * @method $this confirmBtnLabel(string $value) 设置确认编辑按钮名称
 * @method $this confirmBtnIcon(string $value) 设置确认编辑按钮图标
 * @method $this cancelBtnLabel(string $value) 设置取消编辑按钮名称
 * @method $this cancelBtnIcon(string $value) 设置取消编辑按钮图标
 * @method $this needConfirm(bool $value) 设置是否需要确认操作，可用来控控制表格的操作交互
 * @method $this canAccessSuperData(bool $value) 设置是否可以访问父级数据，也就是表单中的同级数据，通常需要跟 strictMode 搭配使用
 * @method $this strictMode(bool $value) 为了性能，默认其他表单项项值变化不会让表格更新，有时为了同步获取其他表单项字段，需要开启这个
 * @method $this minLength(int $value) 设置最小行数, 2.4.1版本后支持变量
 * @method $this maxLength(int $value) 设置最大行数, 2.4.1版本后支持变量
 * @method $this perPage(int $value) 设置每页展示几行数据，如果不配置则不会显示分页器
 * @method $this columns(array $value) 设置列信息
 */
class InputTable extends FormBase
{
    public string $type = 'input-table';
}
