<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\utils;

/**
 * 数组/树形数据处理工具
 */
class DataUtil
{
    /**
     * 二维数组转树形结构（保留层级信息）
     * @param array $data 待处理数据
     * @param int $pid 顶级父级值
     * @param string $html 层级占位符
     * @param string $fieldPri 主键字段
     * @param string $fieldPid 父级字段
     * @param int $level 当前层级
     * @param string $children 子级字段
     * @return array
     */
    public static function channelLevel(
        $data,
        $pid = 0,
        string $html = '&nbsp;',
        string $fieldPri = 'id',
        string $fieldPid = 'pid',
        int $level = 1,
        string $children = 'children'
    ) {
        if (empty($data)) {
            return [];
        }
        $list = [];
        foreach ($data as $value) {
            if (!isset($value[$fieldPid]) || !isset($value[$fieldPri])) {
                continue;
            }
            if ($value[$fieldPid] != $pid) {
                continue;
            }
            $key = $value[$fieldPri];
            $list[$key] = $value;
            $list[$key]['_level'] = $level;
            $list[$key]['_html'] = str_repeat($html, $level - 1);
            $list[$key][$children] = static::channelLevel(
                $data,
                $key,
                $html,
                $fieldPri,
                $fieldPid,
                $level + 1,
                $children
            );
        }
        return $list;
    }

    /**
     * 二维数组转树形结构（重置多层级下标，不带层级信息）
     * @param array $data 待处理数据
     * @param int $pid 顶级父级值
     * @param string $idField 主键字段
     * @param string $pidField 父级字段
     * @param string $children 子级字段
     * @return array
     */
    public static function channelTree(
        array $data,
        int $pid = 0,
        string $idField = 'id',
        string $pidField = 'pid',
        string $children = 'children'
    ) {
        $data = static::channelLevel($data, $pid, '', $idField, $pidField, 1, $children);
        return static::resetKeys($data);
    }

    /**
     * 重置多层级数组下标，数字下标转为列表
     * @param mixed $array
     * @return mixed
     */
    public static function resetKeys($array)
    {
        if (!is_array($array)) {
            return $array;
        }
        $keys = implode('', array_keys($array));
        if ($keys !== '' && is_numeric($keys)) {
            $array = array_values($array);
        }
        return array_map([static::class, 'resetKeys'], $array);
    }
}
