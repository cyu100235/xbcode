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
 * 表单选项组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/options
 * @method $this options(array $value) 设置选项列表
 * @method $this source(string $value) 设置数据源
 * @method $this selectFirst(bool $value) 设置是否选中第一个选项
 * @method $this initFetchOn(string $value) 设置初始化获取数据的条件
 * @method $this initFetch(bool $value) 设置是否在初始化时获取数据
 * @method $this multiple(bool $value) 设置是否支持多选
 * @method $this joinValues(bool $value) 设置是否将选中值连接成字符串
 * @method $this delimiter(string $value) 设置连接字符串的分隔符
 * @method $this extractValue(bool $value) 设置是否提取选中值
 * @method $this clearable(bool $value) 设置是否可清除选中值
 * @method $this resetValue(string $value) 设置重置时的默认值
 * @method $this deferApi(string $value) 设置延迟加载数据的 API
 * @method $this addApi(string $value) 设置添加选项的 API
 * @method $this addControls(array $value) 设置添加选项的控件
 * @method $this creatable(bool $value) 设置是否可创建新选项
 * @method $this createBtnLabel(string $value) 设置创建按钮的标签
 * @method $this editable(bool $value) 设置是否可编辑选项
 * @method $this editApi(string $value) 设置编辑选项的 API
 * @method $this editControls(array $value) 设置编辑选项的控件
 * @method $this removable(bool $value) 设置是否可删除选项
 * @method $this deleteApi(string $value) 设置删除选项的 API
 * @method $this deleteConfirmText(string $value) 设置删除确认文本
 * @method $this autoFill(array $value) 设置自动填充选项
 * @method $this labelField(string $value) 设置选项标签字段
 * @method $this valueField(string $value) 设置选项值字段
 */
class FormOptions extends FormBase
{
    /**
     * 是否选择组件
     * @var bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected bool $isSelect = true;
}
