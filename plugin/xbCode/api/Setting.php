<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\api;

use RuntimeException;

/**
 * 后台配置分组（系统设置选项卡）HTTP 客户端
 *
 * 后台可能独立部署，插件安装时不再直接写配置表，而是把 setting 目录下的分组模板通过 HTTP 上报后台
 * （统一走 Gateway 出口）：
 *   - 写入接口：POST {GATEWAY_URL}/xbAdmin/admin/Config/report（后台按「plugin + group」幂等，存在则更新）
 *
 * 同时对外提供 setting 模板解析方法，供插件安装器与后台配置模型复用，保证两端口径一致。
 *
 * 对外 API：
 *   Setting::install(['system' => Setting::parse($file)], 'xbAdmin');  // 上报分组模板
 *   Setting::fields($components);                                      // 提取分组中的字段组件
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class Setting
{
    /**
     * 上报配置分组模板
     * @param array $groups 分组数据，键为分组标识，值为 Setting::parse() 的返回值
     * @param string $plugin 插件标识
     * @throws RuntimeException
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function install(array $groups, string $plugin = ''): void
    {
        $plugin = trim($plugin);
        if ($plugin === '') {
            throw new RuntimeException('上报配置失败，插件标识错误');
        }
        $groups = array_values(array_filter($groups, function ($group) {
            return is_array($group) && !empty($group['group']) && !empty($group['body']);
        }));
        if (!$groups) {
            return;
        }
        Gateway::request('POST', '/xbAdmin/admin/Config/report', [
            'json' => ['plugin' => $plugin, 'groups' => $groups],
        ]);
    }

    /**
     * 解析单个 setting 模板文件
     *
     * 仅解析以 FieldSet 组件为容器的模板，非 FieldSet 模板返回空数组（不导入配置分组）。
     * @param string $file 模板文件绝对路径
     * @return array ['group' => 分组标识, 'title' => 分组标题, 'sort' => 排序, 'body' => 字段组件]
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function parse(string $file): array
    {
        if (!is_file($file)) {
            return [];
        }
        $components = require $file;
        if (is_object($components) && method_exists($components, 'get')) {
            $components = [$components];
        }
        if (!is_array($components) || !$components) {
            return [];
        }
        $components = self::toArray($components);
        // 只有以 FieldSet 组件承载的模板才是合法的配置分组，其他模板不导入
        if (!self::isFieldSet($components)) {
            return [];
        }
        $group = basename($file, '.php');
        $result = self::normalize($components);
        return [
            'group' => $group,
            'title' => $result['title'] !== '' ? $result['title'] : $group,
            'sort' => $result['sort'],
            'body' => $result['body'],
        ];
    }

    /**
     * 判断分组模板是否由唯一的 FieldSet 组件承载
     * @param array $components
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function isFieldSet(array $components): bool
    {
        if (count($components) !== 1) {
            return false;
        }
        $first = reset($components);
        return is_array($first) && ($first['type'] ?? '') === 'fieldset';
    }

    /**
     * 归一化分组模板
     *
     * 模板顶层只有唯一容器时，用容器标题作分组标题、容器内容作分组字段，
     * 否则整个模板作为一个分组，标题取分组标识。
     * @param array $components
     * @return array ['title' => 标题, 'sort' => 排序, 'body' => 字段组件]
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function normalize(array $components): array
    {
        $components = self::toArray($components);
        $first = reset($components);
        $isWrapper = count($components) === 1
            && is_array($first)
            && !empty($first['title'])
            && !empty($first['body'])
            && is_array($first['body']);
        return [
            'title' => $isWrapper ? (string) $first['title'] : '',
            'sort' => is_array($first) ? (int) ($first['sort'] ?? 0) : 0,
            'body' => $isWrapper ? self::toArray($first['body']) : $components,
        ];
    }

    /**
     * 递归收集带 name 的字段组件，兼容 FieldSet 等容器的 body / items 嵌套
     * @param array $components
     * @return array 键为字段名
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function fields(array $components): array
    {
        $fields = [];
        foreach (self::toArray($components) as $component) {
            if (!is_array($component)) {
                continue;
            }
            $name = trim((string) ($component['name'] ?? ''));
            if ($name !== '') {
                $fields[$name] = $component;
                continue;
            }
            foreach (['body', 'items'] as $key) {
                if (!empty($component[$key]) && is_array($component[$key])) {
                    $fields = array_merge($fields, self::fields($component[$key]));
                }
            }
        }
        return $fields;
    }

    /**
     * 组件对象转数组，模板允许直接返回组件对象
     * @param array $components
     * @return array
     */
    protected static function toArray(array $components): array
    {
        foreach ($components as $key => $component) {
            if (is_object($component) && method_exists($component, 'get')) {
                $components[$key] = $component->get();
            }
        }
        return $components;
    }
}