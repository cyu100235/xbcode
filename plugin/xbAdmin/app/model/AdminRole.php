<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\app\model;

use plugin\xbCode\app\Model;
use plugin\xbAdmin\enum\LayoutEnum;
use plugin\xbAdmin\enum\ThemeEnum;

/**
 * 管理员角色模型
 *
 * rule 字段存放权限路径数组的JSON文本，theme 字段存放部门主题的JSON文本，
 * 这里刻意不使用 think-orm 的 $json 类型转换，全部由控制器/接口显式 encode/decode，
 * 避免「模型自动编码 + 业务代码再编码」造成的双重转义。
 */
class AdminRole extends Model
{
    /**
     * 解析角色权限路径
     * @param mixed $rule rule字段原始值
     * @return array
     */
    public static function parseRules(mixed $rule): array
    {
        if (is_array($rule)) {
            return array_values($rule);
        }
        $rule = (string) $rule;
        if ($rule === '') {
            return [];
        }
        $rules = json_decode($rule, true);
        return is_array($rules) ? array_values($rules) : [];
    }

    /**
     * 序列化角色权限路径
     * @param array $rules 权限路径列表
     * @return string
     */
    public static function stringifyRules(array $rules): string
    {
        $rules = array_values(array_unique(array_map('strval', $rules)));
        sort($rules);
        return (string) json_encode($rules, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * 部门主题的默认值
     *
     * 只包含 config/theme.php 顶层的可配置项，各布局的尺寸由配置文件统一维护，
     * 仅在部门尚未配置主题时用于表单回填展示。
     * @return array
     */
    public static function themeDefault(): array
    {
        return [
            'layout' => LayoutEnum::getColumn('value'),
            'active' => LayoutEnum::DEFAULT['value'],
            'mode' => ThemeEnum::OS['value'],
            'colors' => '',
            'sideColor' => '',
            'collapse' => false,
        ];
    }

    /**
     * 解析部门主题
     * @param mixed $theme theme字段原始值
     * @return array
     */
    public static function parseTheme(mixed $theme): array
    {
        if (is_array($theme)) {
            return $theme;
        }
        $theme = (string) $theme;
        if ($theme === '') {
            return [];
        }
        $theme = json_decode($theme, true);
        return is_array($theme) ? $theme : [];
    }

    /**
     * 归一化部门主题
     *
     * 只保留显式配置且取值合法的字段，未配置的字段不下发，
     * 这样未设置主题的部门仍走内置默认布局，不会因默认值覆盖而改变原有观感。
     * @param mixed $theme theme字段原始值或表单提交数据
     * @return array
     */
    public static function normalizeTheme(mixed $theme): array
    {
        $data = static::parseTheme($theme);
        if (!$data) {
            return [];
        }
        $result = [];
        if (array_key_exists('layout', $data)) {
            $layout = $data['layout'];
            if (!is_array($layout)) {
                // 复选框字段可能以数组、JSON文本或逗号拼接字符串提交
                $decoded = json_decode((string) $layout, true);
                $layout = is_array($decoded) ? $decoded : explode(',', (string) $layout);
            }
            $layout = array_values(array_intersect(LayoutEnum::getColumn('value'), array_map('strval', $layout)));
            // 允许保存为空，前端会据此关闭「主题设置」里的布局切换
            $result['layout'] = $layout;
        }
        if (array_key_exists('active', $data) && in_array((string) $data['active'], LayoutEnum::getColumn('value'), true)) {
            $result['active'] = (string) $data['active'];
        }
        if (array_key_exists('mode', $data) && in_array((string) $data['mode'], ThemeEnum::getColumn('value'), true)) {
            $result['mode'] = (string) $data['mode'];
        }
        foreach (['colors', 'sideColor'] as $field) {
            if (array_key_exists($field, $data)) {
                $result[$field] = trim((string) $data[$field]);
            }
        }
        if (array_key_exists('collapse', $data)) {
            $result['collapse'] = static::toBool($data['collapse']);
        }
        return $result;
    }

    /**
     * 序列化部门主题
     * @param array $theme
     * @return string
     */
    public static function stringifyTheme(array $theme): string
    {
        if (!$theme) {
            return '';
        }
        return (string) json_encode($theme, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * 布尔归一化，兼容 true/false、1/0 与枚举 '20'/'10' 三种形态
     * @param mixed $value
     * @return bool
     */
    protected static function toBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        return in_array((string) $value, ['1', '20', 'true'], true);
    }
}
