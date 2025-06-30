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
 * 树形选择框
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-tree
 * @method $this options(array $value) 选项组
 * @method $this source(string $value) 动态选项组
 * @method $this autoComplete(string $value) 自动提示补全
 * @method $this multiple(bool $value) 是否多选
 * @method $this delimiter(string $value) 拼接符
 * @method $this labelField(string $value) 选项标签字段
 * @method $this valueField(string $value) 选项值字段
 * @method $this iconField(string $value) 图标值字段
 * @method $this deferField(string $value) 懒加载字段
 * @method $this joinValues(bool $value) 拼接值
 * @method $this extractValue(bool $value) 提取值
 * @method $this creatable(bool $value) 新增选项
 * @method $this addControls(array $value) 自定义新增表单项
 * @method $this addApi(string $value) 配置新增选项接口
 * @method $this editable(bool $value) 编辑选项
 * @method $this editControls(array $value) 自定义编辑表单项
 * @method $this editApi(string $value) 配置编辑选项接口
 * @method $this removable(bool $value) 删除选项
 * @method $this deleteApi(string $value) 配置删除选项接口
 * @method $this searchable(bool $value) 是否可检索
 * @method $this hideRoot(bool $value) 如果想要显示个顶级节点，请设置为 false
 * @method $this rootLabel(string $value) 当 hideRoot 不为 false 时有用，用来设置顶级节点的文字
 * @method $this showIcon(bool $value) 是否显示图标
 * @method $this showRadio(bool $value) 是否显示单选按钮，multiple 为 false 是有效
 * @method $this showOutline(bool $value) 是否显示树层级展开线
 * @method $this initiallyOpen(bool $value) 设置是否默认展开所有层级
 * @method $this unfoldedLevel(int $value) 设置默认展开的级数，只有initiallyOpen不是true时生效
 * @method $this autoCheckChildren(bool $value) 当选中父节点时级联选择子节点
 * @method $this cascade(bool $value) autoCheckChildren 为 true 时生效；默认行为：子节点禁用，值只包含父节点值；设置为 true 时，子节点可反选，值包含父子节点值
 * @method $this withChildren(bool $value) cascade 为 false 时生效，选中父节点时，值里面将包含父子节点的值，否则只会保留父节点的值
 * @method $this onlyChildren(bool $value) autoCheckChildren 为 true 时生效，不受 cascade 影响；onlyChildren 为 true，ui 行为级联选中子节点，子节点可反选，值只包含子节点的值
 * @method $this onlyLeaf(bool $value) 只允许选择叶子节点
 * @method $this rootCreatable(bool $value) 是否可以创建顶级节点
 * @method $this rootCreateTip(string $value) 创建顶级节点的悬浮提示
 * @method $this minLength(int $value) 最少选中的节点数
 * @method $this maxLength(int $value) 最多选中的节点数
 * @method $this treeContainerClassName(string $value) tree 最外层容器类名
 * @method $this enableNodePath(bool $value) 是否开启节点路径模式
 * @method $this pathSeparator(string $value) 节点路径的分隔符，enableNodePath为true时生效
 * @method $this highlightTxt(string $value) 标签中需要高亮的字符，支持变量
 * @method $this itemHeight(int $value) 每个选项的高度，用于虚拟渲染
 * @method $this virtualThreshold(int $value) 在选项数量超过多少时开启虚拟渲染
 * @method $this menuTpl(string $value) 选项自定义渲染 HTML 片段
 * @method $this enableDefaultIcon(bool $value) 是否为选项添加默认的前缀 Icon，父节点默认为folder，叶节点默认为file
 * @method $this heightAuto(bool $value) 默认高度会有个 maxHeight，即超过一定高度就会内部滚动，如果希望自动增长请设置此属性
 * @method $this nodeBehavior(array $value) 节点行为配置，支持配置多个行为
 * @method $this autoCancelParent(bool $value) 子节点取消时自动取消父节点的值，仅在多选且 cascade 为 true 时生效
 * @method $this toolbar(array $value) 工具栏区域，仅开启检索时生效
 * @method $this toolbarClassName(string $value) 工具栏区域类名
 * @method $this itemActions(mixed $value) 节点操作栏区域
 */
class InputTree extends FormOptions
{
    public string $type = 'input-tree';

    /**
     * 展开树形选择框
     * @return static
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     */
    public function inputTree()
    {
        $this->type = 'input-tree';
        return $this;
    }

    /**
     * 树形选择框
     * @return static
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     */
    public function treeSelect()
    {
        $this->type = 'tree-select';
        return $this;
    }
}
