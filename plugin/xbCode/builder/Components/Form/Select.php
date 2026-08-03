<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Components\Form;

/**
 * 下拉选择器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/select
 * @method $this options(array $value) 选项组
 * @method $this source(string $source) 动态选项组
 * @method $this autoComplete(string $autoComplete) 自动提示补全
 * @method $this delimiter(string $delimiter) 拼接符
 * @method $this labelField(string $labelField) 选项标签字段
 * @method $this valueField(string $valueField) 选项值字段
 * @method $this unique(bool $value) 是否唯一
 * @method $this joinValues(bool $joinValues) 拼接值
 * @method $this extractValue(bool $extractValue) 提取值
 * @method $this checkAll(bool $checkAll) 是否支持全选
 * @method $this checkAllLabel(string $checkAllLabel) 全选的文字
 * @method $this checkAllBySearch(bool $checkAllBySearch) 有检索时只全选检索命中的项
 * @method $this defaultCheckAll(bool $defaultCheckAll) 默认是否全选
 * @method $this creatable(bool $creatable) 新增选项
 * @method $this multiple(bool $multiple) 多选
 * @method $this searchable(bool $searchable) 检索
 * @method $this filterOption(string $filterOption) 过滤选项
 * @method $this createBtnLabel(string $createBtnLabel) 新增选项
 * @method $this addControls(array $addControls) 自定义新增表单项
 * @method $this addApi(string $addApi) 配置新增选项接口
 * @method $this editable(bool $editable) 编辑选项
 * @method $this editControls(array $editControls) 自定义编辑表单项
 * @method $this editApi(string $editApi) 配置编辑选项接口
 * @method $this removable(bool $removable) 删除选项
 * @method $this deleteApi(string $deleteApi) 配置删除选项接口
 * @method $this autoFill(array $autoFill) 自动填充
 * @method $this menuTpl(string $menuTpl) 支持配置自定义菜单
 * @method $this clearable(bool $clearable) 单选模式下是否支持清空
 * @method $this hideSelected(bool $hideSelected) 隐藏已选选项
 * @method $this mobileClassName(string $mobileClassName) 移动端浮层类名
 * @method $this selectMode(string $selectMode) 可选：group、table、tree、chained、associated。分别为：列表形式、表格形式、树形选择形式、级联选择形式，关联选择形式（与级联选择的区别在于，级联是无限极，而关联只有一级，关联左边可以是个 tree）。
 * @method $this searchResultMode(string $searchResultMode) 如果不设置将采用 selectMode 的值，可以单独配置，参考 selectMode，决定搜索结果的展示形式。
 * @method $this columns(array $columns) 当展示形式为 table 可以用来配置展示哪些列，跟 table 中的 columns 配置相似，只是只有展示功能。
 * @method $this leftOptions(array $leftOptions) 当展示形式为 associated 时用来配置左边的选项集。
 * @method $this leftMode(string $leftMode) 当展示形式为 associated 时用来配置左边的选择形式，支持 list 或者 tree。默认为 list。
 * @method $this rightMode(string $rightMode) 当展示形式为 associated 时用来配置右边的选择形式，可选：list、table、tree、chained。
 * @method $this maxTagCount(int $maxTagCount) 标签的最大展示数量，超出数量后以收纳浮层的方式展示，仅在多选模式开启后生效
 * @method $this overflowTagPopover(array $overflowTagPopover) 收纳浮层的配置属性，详细配置参考Tooltip
 * @method $this optionClassName(string $optionClassName) 选项 CSS 类名
 * @method $this popOverContainerSelector(string $popOverContainerSelector) 弹层挂载位置选择器，会通过querySelector获取
 * @method $this clearable(bool $clearable) 是否展示清空图标
 * @method $this overlay(array $overlay) 弹层宽度与对齐方式 2.8.0 以上版本
 * @method $this showInvalidMatch(bool $showInvalidMatch) 选项值与选项组不匹配时选项值是否飘红
 * @method $this noResultsText(string $noResultsText) 无结果时的文本
 */
class Select extends FormOptions
{
    public string $type = 'select';

    public function defaultAttr()
    {
        $this->joinValues(false)->extractValue(true);
    }

    public function buttonGroupSelect()
    {
        $this->type("button-group-select");
        return $this;
    }

    /**
     * 选择事件
     * @param string $actionType 事件动作
     * - `clear` 清空时动作
     * - `reset` 将值重置为初始值
     * - `reload` 重新加载，调用 source，刷新数据域数据刷新
     * - `setValue` 更新数据，开启multiple支持设置多项，开启joinValues时，多值用,分隔，否则多值用数组
     * @param string $target 目标组件名称
     * @param array $args 动作参数
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function onChangeEvent(string $actionType, string $target, array $args = [])
    {
        $events = $this->onEvent;
        $events['change']['actions'][] = array_merge($args, [
            'actionType' => $actionType,
            'target' => $target,
        ]);
        $this->onEvent($events);
        return $this;
    }

    /**
     * 失去焦点事件
     * @param string $actionType 事件动作
     * - `clear` 清空时动作
     * - `reset` 将值重置为初始值
     * - `reload` 重新加载，调用 source，刷新数据域数据刷新
     * - `setValue` 更新数据，开启multiple支持设置多项，开启joinValues时，多值用,分隔，否则多值用数组
     * @param string $target 目标组件名称
     * @param array $args 动作参数
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function onBlurEvent(string $actionType, string $target, array $args = [])
    {
        $events = $this->onEvent;
        $events['blur']['actions'][] = array_merge($args, [
            'actionType' => $actionType,
            'target' => $target,
        ]);
        $this->onEvent($events);
        return $this;
    }

    /**
     * 获取焦点事件
     * @param string $actionType 事件动作
     * @param string $target 目标组件名称
     * - `clear` 清空时动作
     * - `reset` 将值重置为初始值
     * - `reload` 重新加载，调用 source，刷新数据域数据刷新
     * - `setValue` 更新数据，开启multiple支持设置多项，开启joinValues时，多值用,分隔，否则多值用数组
     * @param array $args 动作参数
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function onFocusEvent(string $actionType, string $target, array $args = [])
    {
        $events = $this->onEvent;
        $events['focus']['actions'][] = array_merge($args, [
            'actionType' => $actionType,
            'target' => $target,
        ]);
        $this->onEvent($events);
        return $this;
    }

    /**
     * 新增项确认时触发
     * @param string $actionType 事件动作
     * - `clear` 清空时动作
     * - `reset` 将值重置为初始值
     * - `reload` 重新加载，调用 source，刷新数据域数据刷新
     * - `setValue` 更新数据，开启multiple支持设置多项，开启joinValues时，多值用,分隔，否则多值用数组
     * @param string $target 目标组件名称
     * @param array $args 动作参数
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function onAddConfirmEvent(string $actionType, string $target, array $args = [])
    {
        $events = $this->onEvent;
        $events['addConfirm']['actions'][] = array_merge($args, [
            'actionType' => $actionType,
            'target' => $target,
        ]);
        $this->onEvent($events);
        return $this;
    }

    /**
     * 编辑项确认时触发
     * @param string $actionType 事件动作
     * - `clear` 清空时动作
     * - `reset` 将值重置为初始值
     * - `reload` 重新加载，调用 source，刷新数据域数据刷新
     * - `setValue` 更新数据，开启multiple支持设置多项，开启joinValues时，多值用,分隔，否则多值用数组
     * @param string $target 目标组件名称
     * @param array $args 动作参数
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function onEditConfirmEvent(string $actionType, string $target, array $args = [])
    {
        $events = $this->onEvent;
        $events['editConfirm']['actions'][] = array_merge($args, [
            'actionType' => $actionType,
            'target' => $target,
        ]);
        $this->onEvent($events);
        return $this;
    }

    /**
     * 删除项确认时触发
     * @param string $actionType 事件动作
     * - `clear` 清空时动作
     * - `reset` 将值重置为初始值
     * - `reload` 重新加载，调用 source，刷新数据域数据刷新
     * - `setValue` 更新数据，开启multiple支持设置多项，开启joinValues时，多值用,分隔，否则多值用数组
     * @param string $target 目标组件名称
     * @param array $args 动作参数
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function onDeleteConfirmEvent(string $actionType, string $target, array $args = [])
    {
        $events = $this->onEvent;
        $events['deleteConfirm']['actions'][] = array_merge($args, [
            'actionType' => $actionType,
            'target' => $target,
        ]);
        $this->onEvent($events);
        return $this;
    }

    /**
     * 新增项时触发
     * @param string $actionType 事件动作
     * - `clear` 清空时动作
     * - `reset` 将值重置为初始值
     * - `reload` 重新加载，调用 source，刷新数据域数据刷新
     * - `setValue` 更新数据，开启multiple支持设置多项，开启joinValues时，多值用,分隔，否则多值用数组
     * @param string $target 目标组件名称
     * @param array $args 动作参数
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function onAddEvent(string $actionType, string $target, array $args = [])
    {
        $events = $this->onEvent;
        $events['add']['actions'][] = array_merge($args, [
            'actionType' => $actionType,
            'target' => $target,
        ]);
        $this->onEvent($events);
        return $this;
    }

    /**
     * 编辑项时触发
     * @param string $actionType 事件动作
     * - `clear` 清空时动作
     * - `reset` 将值重置为初始值
     * - `reload` 重新加载，调用 source，刷新数据域数据刷新
     * - `setValue` 更新数据，开启multiple支持设置多项，开启joinValues时，多值用,分隔，否则多值用数组
     * @param string $target 目标组件名称
     * @param array $args 动作参数
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function onEditEvent(string $actionType, string $target, array $args = [])
    {
        $events = $this->onEvent;
        $events['edit']['actions'][] = array_merge($args, [
            'actionType' => $actionType,
            'target' => $target,
        ]);
        $this->onEvent($events);
        return $this;
    }

    /**
     * 删除项时触发
     * @param string $actionType 事件动作
     * - `clear` 清空时动作
     * - `reset` 将值重置为初始值
     * - `reload` 重新加载，调用 source，刷新数据域数据刷新
     * - `setValue` 更新数据，开启multiple支持设置多项，开启joinValues时，多值用,分隔，否则多值用数组
     * @param string $target 目标组件名称
     * @param array $args 动作参数
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function onDelEvent(string $actionType, string $target, array $args = [])
    {
        $events = $this->onEvent;
        $events['del']['actions'][] = array_merge($args, [
            'actionType' => $actionType,
            'target' => $target,
        ]);
        $this->onEvent($events);
        return $this;
    }
}
