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
 * 组合控件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/combo
 * @method $this formClassName(string $value) 单组表单项的类名
 * @method $this items(array $value) 组合展示的表单项
 * @method $this itemsColumnClassName(string $value) 列的类名，可以用它配置列宽度。默认平均分配。
 * @method $this itemsUnique(bool $value) 设置当前列值是否唯一，即不允许重复选择。
 * @method $this noBorder(bool $value) 单组表单项是否显示边框
 * @method $this scaffold(array $value) 单组表单项初始值
 * @method $this multiple(bool $value) 是否多选
 * @method $this multiLine(bool $value) 默认是横着展示一排，设置以后竖着展示
 * @method $this minLength(int $value) 最少添加的条数
 * @method $this maxLength(int $value) 最多添加的条数
 * @method $this flat(bool $value) 是否将结果扁平化(去掉 name),只有当 items 的 length 为 1 且 multiple 为 true 的时候才有效。
 * @method $this joinValues(bool $value) 默认为 true 当扁平化开启的时候，是否用分隔符的形式发送给后端，否则采用 array 的方式
 * @method $this delimiter(string $value) 当扁平化开启并且 joinValues 为 true 时，用什么分隔符。
 * @method $this addable(bool $value) 是否可新增
 * @method $this addattop(bool $value) 在顶部添加
 * @method $this removable(bool $value) 是否可删除
 * @method $this deleteApi(string $value) 如果配置了，则删除前会发送一个 api，请求成功才完成删除
 * @method $this deleteConfirmText(string $value) 当配置 deleteApi 才生效！删除时用来做用户确认
 * @method $this draggable(bool $value) 是否可以拖动排序, 需要注意的是当启用拖动排序的时候，会多一个$id 字段
 * @method $this draggableTip(string $value) 可拖拽的提示文字
 * @method $this subFormMode(string $value) 可选normal、horizontal、inline
 * @method $this subFormHorizontal(array $value) 当 subFormMode 为 horizontal 时有用，用来控制 label 的展示占比
 * @method $this placeholder(string $value) 没有成员时显示。
 * @method $this canAccessSuperData(bool $value) 指定是否可以自动获取上层的数据并映射到表单项上
 * @method $this conditions(array $value) 数组的形式包含所有条件的渲染类型，单个数组内的test 为判断条件，数组内的items为符合该条件后渲染的schema
 * @method $this typeSwitchable(bool $value) 是否可切换条件，配合conditions使用
 * @method $this strictMode(bool $value) 默认为严格模式，设置为 false 时，当其他表单项更新是，里面的表单项也可以及时获取，否则不会。
 * @method $this syncFields(array $value) 配置同步字段。只有 strictMode 为 false 时有效。如果 Combo 层级比较深，底层的获取外层的数据可能不同步。但是给 combo 配置这个属性就能同步下来。输入格式：["os"]
 * @method $this nullable(bool $value) 允许为空，如果子表单项里面配置验证器，且又是单条模式。可以允许用户选择清空（不填）。
 * @method $this itemClassName(string $value) 单组 CSS 类
 * @method $this itemsWrapperClassName(string $value) 组合区域 CSS 类
 * @method $this deleteBtn(mixed $value) 自定义删除按钮	只有当removable为 true 时有效; 如果为string则为按钮的文本；如果为Button则根据配置渲染删除按钮。
 * @method $this addBtn(mixed $value) 自定义新增按钮	可新增自定义配置渲染新增按钮，在tabsMode: true下不生效。
 * @method $this addButtonClassName(string $value) 新增按钮 CSS 类名
 * @method $this addButtonText(string $value) 新增按钮文字
 */
class Combo extends FormBase
{
    public string $type = 'combo';
}
