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

use Closure;
use JsonSerializable;

/**
 * BaseSchema 基础渲染器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link http://www.xbcode.net
 * @method $this type(string $value) 渲染器类型
 * @method $this className(string $value) 外层 Dom 的类名
 * @method $this style(array $value) 外层 Dom 的样式
 * @method $this ref(string $value) 组件引用
 * @method $this disabled(bool $value) 是否禁用
 * @method $this disabledOn(string $value) 禁用条件
 * @method $this disabledTip(string $value) 按钮失效提示
 * @method $this hidden(bool $value) 是否隐藏
 * @method $this hiddenOn(string $value) 隐藏条件
 * @method $this visible(bool $value) 是否可见
 * @method $this visibleOn(string $value) 可见条件
 * @method $this id(string $value) 组件ID
 * @method $this value(mixed $value) 组件值
 * @method $this onEvent(array $value) 事件配置
 * @method $this getValue(mixed $value) 组件赋值时自定义处理
 * @method $this setValue(mixed $value) 组件赋值提交时自定义处理
 * @method $this onDelete(mixed $value) 删除时自定义处理
 * @method $this defaultAttr() 默认属性
 */
class BaseSchema implements JsonSerializable
{
    /**
     * 渲染器类型
     * @var string
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     */
    public string $type;

    /**
     * 创建组件
     * @return BaseSchema
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     */
    public static function make(): static
    {
        return new static();
    }

    /**
     * 设置组件属性
     * @param string $name
     * @param mixed $value
     * @return static
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     */
    public function setVariable(string $name, mixed $value)
    {
        $this->$name = $value;
        return $this;
    }

    /**
     * 批量设置组件属性
     * @param array $data
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setVariables(array $data)
    {
        foreach ($data as $name => $value) {
            $this->setVariable($name, $value);
        }
        return $this;
    }

    /**
     * 获取组件属性
     * @param string $name
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     */
    public function getVariable(string $name)
    {
        return data_get($this, $name) ?: null;
    }

    /**
     * 获取组件所有属性
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getVariables()
    {
        if (method_exists($this::class, 'defaultAttr')) {
            $this->defaultAttr();
        }
        return get_object_vars($this);
    }

    /**
     * 设置组件属性
     * @param string $name
     * @param mixed $value
     * @return static
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     */
    public function __set(string $name, $value)
    {
        $this->setVariable($name, $value);
        return $this;
    }

    /**
     * 转换为多层级数组
     * @param array $data
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     */
    protected function arrayToNested(array $data)
    {
        $result = null;
        foreach (array_reverse($data) as $v) {
            $result = [$v => $result];
        }
        return $result;
    }

    /**
     * 获取组件属性
     * @param mixed $name
     * @return void
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     */
    public function __get($name)
    {
        return $this->getVariable($name);
    }

    /**
     * 设置组件属性
     * @param mixed $name
     * @param mixed $arguments
     * @throws \InvalidArgumentException
     * @return static
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     */
    public function __call($name, $arguments)
    {
        if (count($arguments) !== 1) {
            // 获取类名称
            $className = (new \ReflectionClass($this))->getShortName();
            throw new \InvalidArgumentException("类{$className} 方法{$name}参数错误");
        }
        $argument = $arguments[0];
        if ($argument instanceof Closure) {
            $argument = $argument();
        }
        $this->$name = $argument;
        return $this;
    }

    /**
     * 组件序列化
     * @return array
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     */
    public function jsonSerialize(): array
    {
        return $this->getVariables();
    }
}
