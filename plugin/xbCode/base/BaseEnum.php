<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\base;

use Exception;
use plugin\xbCode\api\Dict;

/**
 * 枚举基类
 * 子类示例：
 * ```php
 * class StateEnum extends BaseEnum
 * {
 *     const STATE10 = ['label' => '禁用', 'value' => '10', 'style' => '<span class="label label-danger">禁用</span>'];
 *     const STATE20 = ['label' => '启用', 'value' => '20', 'style' => '<span class="label label-success">启用</span>'];
 * }
 * ```
 * 枚举数据优先取自后台字典（管理员可在后台调整标签与样式），
 * 后台未部署、未收录该枚举或接口异常时，回退到枚举类常量。
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class BaseEnum
{
    /**
     * 获取开关枚举
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function switch()
    {
        $data = [
            'onText' => static::getFieldValue('20', null, 'label'),
            'offText' => static::getFieldValue('10', null, 'label'),
            'trueValue' => static::getFieldValue('20', null, 'value'),
            'falseValue' => static::getFieldValue('10', null, 'value'),
        ];
        return $data;
    }

    /**
     * 获取样式枚举
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function style()
    {
        $data = static::toArray();
        $list = [];
        foreach ($data as $value) {
            $list[$value['value']] = [
                'type' => $value['label'],
            ];
        }
        return $list;
    }

    /**
     * 获取枚举字段值
     * @param string $field
     * @throws Exception
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function dict(string $field = 'style')
    {
        $list = static::toArray();
        if (empty($list)) {
            throw new Exception('枚举类 ' . static::class . ' 没有定义枚举');
        }
        foreach ($list as $value) {
            if (empty($value[$field])) {
                throw new Exception('枚举类 ' . static::class . ' 没有定义样式');
            }
        }
        $data = [];
        foreach ($list as $value) {
            $data[$value['value']] = $value[$field] ?? '';
        }
        return $data;
    }

    /**
     * 获取状态枚举
     * @throws Exception
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function status()
    {
        $data = static::toArray();
        if (empty($data)) {
            throw new Exception('枚举类 ' . static::class . ' 没有定义枚举');
        }
        foreach ($data as $value) {
            if (empty($value['icon'])) {
                throw new Exception('枚举类 ' . static::class . ' 没有状态图标');
            }
            if (empty($value['label'])) {
                throw new Exception('枚举类 ' . static::class . ' 没有状态标签');
            }
        }
        $data = array_column($data, null, 'value');
        return $data;
    }

    /**
     * 获取选项枚举
     * @param callable|null $callback
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function options(?callable $callback = null)
    {
        $data = static::toArray();
        if ($callback && is_callable($callback)) {
            $data = array_map($callback, $data);
        }
        return $data;
    }

    /**
     * 获取枚举字段列
     * @param string $name
     * @param string $value
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getColumn(string $name, ?string $value = null)
    {
        $data = static::toArray();
        $data = array_column($data, $name, $value);
        return $data;
    }

    /**
     * 获取枚举字段值
     * @param string $value
     * @param mixed $default
     * @param string $field
     * @copyright 贵州积木云网络有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getFieldValue(string $value, mixed $default = '', string $field = 'label')
    {
        $data = static::toArray();
        $dict = array_column($data, null, 'value');
        return isset($dict[$value][$field]) ? $dict[$value][$field] : $default;
    }

    /**
     * 转换为数组
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function toArray()
    {
        $className = basename(str_replace("\\", '/', get_called_class()));
        $data = static::getEnumData();
        $list = [];
        foreach ($data as $name => $value) {
            if (!isset($value['label'])) {
                throw new Exception("{$className} 枚举错误，缺少label字段");
            }
            if (!isset($value['value'])) {
                throw new Exception("{$className} 枚举错误，缺少value字段");
            }
            // 追加键名
            $value['key'] = $name;
            // 添加到列表
            $list[] = $value;
        }
        return $list;
    }

    /**
     * 获取枚举数据
     *
     * 优先读取后台字典数据，取不到时回退到枚举类常量。
     * @return array 以常量名为键的枚举数据
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private static function getEnumData()
    {
        // 优先读取后台字典，管理员可在后台调整标签与样式
        $data = static::dictData();
        if ($data) {
            return $data;
        }
        // 反射当前类
        $reflect = new \ReflectionClass(static::class);
        // 获取常量
        $data = $reflect->getConstants();
        // 返回数据
        return $data;
    }

    /**
     * 通过 gateway 读取后台字典数据
     *
     * 枚举类命名空间固定为 plugin\{插件标识}\enum\{枚举类名}，据此定位后台字典；
     * 命名空间不符、后台未收录该枚举或接口异常时返回空数组，由调用方回退本地常量。
     * @return array 以常量名为键的字典数据
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private static function dictData()
    {
        if (!preg_match('#^plugin\\\\([\w\-]+)\\\\enum\\\\([\w]+)$#', static::class, $match)) {
            return [];
        }
        try {
            $values = Dict::values($match[1], $match[2]);
        } catch (\Throwable $e) {
            return [];
        }
        $data = [];
        foreach ($values as $value) {
            // key 即枚举常量名，缺失时该行不可用
            if (!is_array($value) || empty($value['key'])) {
                continue;
            }
            $data[(string) $value['key']] = $value;
        }
        return $data;
    }
}
