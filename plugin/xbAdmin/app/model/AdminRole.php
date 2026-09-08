<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\app\model;

use plugin\xbCode\app\Model;

/**
 * 管理员角色模型
 *
 * rule 字段存放权限路径数组的JSON文本，
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
}
