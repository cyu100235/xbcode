<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Components\Form;

use plugin\xbCode\builder\Components\BaseSchema;

/**
 * 表单基类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/index
 * @method $this size(string $value) 设置大小，支持 xs、sm、base、md、lg
 * @method $this label(string $value) 设置标签
 * @method $this labelClassName(string $value) 设置标签 CSS 类名
 * @method $this name(string $value) 设置字段名
 * @method $this remark(string $value) 设置备注
 * @method $this clearable(string $value) 设置是否可清除
 * @method $this labelRemark(string $value) 设置标签备注
 * @method $this hint(string $value) 设置提示信息
 * @method $this submitOnChange(string $value) 设置是否在值变化时提交
 * @method $this readOnly(string $value) 设置是否只读
 * @method $this readOnlyOn(string $value) 设置只读条件
 * @method $this validateOnChange(string $value) 设置是否在值变化时验证
 * @method $this description(string $value) 设置描述
 * @method $this descriptionClassName(string $value) 设置描述 CSS 类名
 * @method $this mode(string $value) 设置展示模式
 * @method $this horizontal(string $value) 设置是否水平布局
 * @method $this inline(string $value) 设置是否内联布局
 * @method $this inputClassName(string $value) 设置输入框 CSS 类名
 * @method $this placeholder(string $value) 设置占位符
 * @method $this required(bool $value) 设置是否必填
 * @method $this requiredOn(string $value) 设置必填条件
 * @method $this validationErrors(array $value) 设置验证错误信息
 * @method $this validations(array $value) 设置验证规则
 * @method $this value(mixed $value) 设置默认值
 * @method $this clearValueOnHidden(string $value) 设置隐藏时是否清除值
 * @method $this validateApi(string|int $value) 设置验证 API
 * @method $this columnRatio(string|int $value) 设置宽度占用比率
 * @method $this static(bool $value) 是否静态展示
 * @method $this componentId(string $value) 组件唯一ID
 */
class FormBase extends BaseSchema
{
    /**
     * 组件类型
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public string $type = "input-text";

    /**
     * 是否选择组件
     * @var bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected bool $isSelect = false;

    /**
     * 添加验证规则
     * @desc 验证规则地址
     * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/formitem#支持的格式校验
     * @param string $rule 规则名称
     * - `isRequired` 是否必填
     * @param mixed $value 规则参数
     * - `true` isRequired的参数
     * @param string $message 错误提示
     * - `message` 请填写XX参数
     * @return static
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    public function addValidate(string $rule, mixed $value, string $message)
    {
        if (empty($this->validations[$rule])) {
            $this->setVariable('validations', []);
            $this->setVariable('validationErrors', []);
        }
        $this->validations[$rule] = $value;
        $this->validationErrors[$rule] = $message;
        return $this;
    }

    /**
     * 获取组件所有属性
     * @return array
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    public function get()
    {
        if (!empty($this->label) && empty($this->placeholder)) {
            $prefix = $this->isSelect ? '请选择' : '请填写';
            $placeholder = "{$prefix}{$this->label}";
            $this->placeholder($placeholder);
        }
        unset($this->isSelect);
        return parent::get();
    }

    /**
     * 获取是否选择类型组件
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getComponentType(): bool
    {
        return $this->isSelect;
    }

    /**
     * JSON序列化
     * @return array
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    public function jsonSerialize(): array
    {
        return $this->get();
    }
}
