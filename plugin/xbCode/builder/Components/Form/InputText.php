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
 * 输入文本组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-text
 * @method $this options(array $value) 选项组
 * @method $this copyable(array $value) 复制选项配置
 * @method $this source(string|array $value) 动态选项组
 * @method $this autoComplete(string|array $value) 自动补全
 * @method $this multiple(bool $value) 是否多选
 * @method $this delimiter(string $value) 拼接符
 * @method $this labelField(string $value) 选项标签字段
 * @method $this valueField(string $value) 选项值字段
 * @method $this joinValues(bool $value) 拼接值
 * @method $this extractValue(bool $value) 提取值
 * @method $this addOn(array $value) 输入框附加组件，比如附带一个提示文字，或者附带一个提交按钮。
 * @method $this trimContents(bool $value) 是否去除首尾空白文本
 * @method $this clearValueOnEmpty(bool $value) 文本内容为空时去掉这个值
 * @method $this creatable(bool $value) 是否可以创建，默认为可以，除非设置为 false 即只能选择选项中的值
 * @method $this clearable(bool $value) 是否可清除
 * @method $this resetValue(string $value) 清除后设置此配置项给定的值
 * @method $this prefix(string $value) 前缀
 * @method $this suffix(string $value) 后缀
 * @method $this showCounter(bool $value) 是否显示计数器
 * @method $this minLength(int $value) 限制最小字数
 * @method $this maxLength(int $value) 限制最大字数
 * @method $this transform(array $value) 自动转换值，可选 transform: { lowerCase: true, upperCase: true }
 * @method $this borderMode(string $value) 输入框边框模式，全边框，还是半边框，或者没边框
 * @method $this inputControlClassName(string $value) control 节点的 CSS 类名
 * @method $this nativeInputClassName(string $value) 原生 input 标签的 CSS 类名
 * @method $this nativeAutoComplete(string $value) 原生 input 标签的 autoComplete 属性，比如配置集成 new-password
 */
class InputText extends FormBase
{
    /**
     * 输入框类型
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public string $type = 'input-text';

    /**
     * 邮箱类型
     * @return InputText
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function email(): InputText
    {
        $this->type = 'input-email';
        return $this;
    }

    /**
     * URL 类型
     * @return InputText
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function url(): InputText
    {
        $this->type = 'input-url';
        return $this;
    }

    /**
     * 密码类型
     * @return InputText
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function password(): InputText
    {
        $this->type = 'input-password';
        return $this;
    }

    /**
     * 日期类型
     * @return InputText
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function date(): InputText
    {
        $this->type = 'native-date';
        return $this;
    }

    /**
     * 时间类型
     * @return InputText
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function time(): InputText
    {
        $this->type = 'native-time';
        return $this;
    }

    /**
     * 数字类型
     * @return InputText
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function number(): InputText
    {
        $this->type = 'native-number';
        return $this;
    }
}
