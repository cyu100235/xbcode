<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\app\model;

use plugin\xbCode\app\Model;
use plugin\xbAdmin\enum\YesEnum;
use plugin\xbAdmin\exception\business\ExceptionBusiness;

/**
 * 字典模型
 *
 * 每行对应一个枚举类，以「插件标识 + 枚举标识」为唯一身份，枚举标识即枚举类名，
 * values 列存该枚举类全部常量的 JSON 数组，数组元素形如
 * {"key":"SIDE_BAR","label":"侧边双栏","value":"sideBar","style":"..."}，
 * 其中 key 即枚举常量名，同组内唯一，作为枚举合并时的冻结依据。
 *
 * 枚举派生的字典标记 is_system=20：常量集合以枚举上报的 key 为准，已存在的常量一律保留库中数据，
 * 避免插件重复安装覆盖管理员调整过的标签与样式；后台只允许调整 label、style、title、sort，
 * 禁止新增或改名常量。表数据由插件安装时经 HTTP 上报写入，因此后台与插件可以分服务器部署。
 */
class Dict extends Model
{
    /**
     * 设置字典数据列表
     * @param mixed $value
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function setValuesAttr($value)
    {
        if ($value && is_array($value)) {
            $value = Dict::encodeValues($value);
        }
        return $value;
    }

    /**
     * 获取字典数据列表
     * @param mixed $value
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getValuesAttr($value)
    {
        if ($value && is_string($value)) {
            $value = static::decodeValues($value);
        }
        return $value;
    }

    /**
     * 读取枚举选项（枚举类名去重），用于列表筛选下拉
     * @return array [['label' => 枚举标题, 'value' => 枚举标识], ...]
     */
    public static function names(): array
    {
        $list = [];
        foreach (static::field('name,title')->order('id asc')->select()->toArray() as $row) {
            $name = (string) ($row['name'] ?? '');
            if ($name === '' || isset($list[$name])) {
                continue;
            }
            $title = trim((string) ($row['title'] ?? ''));
            $list[$name] = ['label' => $title !== '' ? $title : $name, 'value' => $name];
        }
        return array_values($list);
    }
    
    /**
     * 获取插件选项
     * @return array [['label' => 插件标识, 'value' => 插件标识], ...]
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function plugins(): array
    {
        $data = static::field('plugin')->select()->toArray();
        $list = [];
        foreach ($data as $row) {
            $plugin = (string) ($row['plugin'] ?? '');
            if ($plugin === '' || isset($list[$plugin])) {
                continue;
            }
            $list[$plugin] = ['label' => $plugin, 'value' => $plugin];
        }
        return array_values($list);
    }
    
    /**
     * 组装values列JSON数组
     * @param array $items
     * @return string
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function encodeValues(array $items): string
    {
        $list = [];
        foreach (array_values($items) as $item) {
            if (!is_array($item)) {
                continue;
            }
            $key = trim((string) ($item['key'] ?? ''));
            $label = trim((string) ($item['label'] ?? ''));
            $value = (string) ($item['value'] ?? '');
            // 忽略表格中的空行
            if ($key === '' && $label === '' && $value === '') {
                continue;
            }
            $list[] = [
                'key' => $key,
                'label' => $label,
                'value' => $value,
                'style' => (string) ($item['style'] ?? ''),
            ];
        }
        return (string) json_encode($list, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * 解析 values 列 JSON 数组，非法数据回落为空数组
     * @param string $json
     * @return array 常量列表，每项含 key / label / value / style
     */
    public static function decodeValues(string $json): array
    {
        $items = json_decode($json, true);
        if (!is_array($items)) {
            return [];
        }
        // 兼容早期按常量拆分存储的单对象结构
        if (isset($items['key']) || isset($items['value'])) {
            $items = [$items];
        }
        $list = [];
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }
            $list[] = [
                'key' => (string) ($item['key'] ?? ''),
                'label' => (string) ($item['label'] ?? ''),
                'value' => (string) ($item['value'] ?? ''),
                'style' => (string) ($item['style'] ?? ''),
            ];
        }
        return $list;
    }

    /**
     * 注册插件上报的字典数据
     *
     * 按「插件 + 枚举标识」幂等：一个枚举类一行，values 存该枚举全部常量的 JSON 数组。
     * 组内常量的 key 集合以枚举上报为准，已存在的常量保留库中数据，不覆盖管理员调整过的
     * 标签与样式；新增常量写入枚举的初始值。
     *
     * 已存在的枚举行同样保留库中的 title 与 sort，避免重复安装覆盖后台调整过的枚举标题与排序；
     * 仅首次入库时写入上报的 title，并按枚举声明顺序写入 sort。
     * @param string $plugin 插件标识
     * @param array $groups 上报的分组数据，每项含 name / title / items
     * @throws ExceptionBusiness
     * @return int 本次写入的枚举常量条数
     */
    public static function register(string $plugin, array $groups): int
    {
        $count = 0;
        foreach (array_values($groups) as $index => $item) {
            if (!is_array($item)) {
                throw new ExceptionBusiness('字典分组数据格式错误');
            }
            $name = trim((string) ($item['name'] ?? ''));
            if (!preg_match('/^[\w\-]+$/', $name)) {
                throw new ExceptionBusiness('字典分组标识错误');
            }
            $items = $item['items'] ?? [];
            if (!is_array($items) || !$items) {
                throw new ExceptionBusiness('字典项不能为空：' . $name);
            }
            $title = trim((string) ($item['title'] ?? ''));
            // 该枚举类已有的一行记录，其常量按 key 索引
            $model = static::where('plugin', $plugin)->where('name', $name)->order('id asc')->find();
            $exists = [];
            if ($model) {
                foreach (static::decodeValues((string) $model['values']) as $row) {
                    $key = (string) ($row['key'] ?? '');
                    if ($key !== '' && !isset($exists[$key])) {
                        $exists[$key] = $row;
                    }
                }
            }
            $values = [];
            foreach (array_values($items) as $row) {
                if (!is_array($row)) {
                    throw new ExceptionBusiness('字典项数据格式错误：' . $name);
                }
                $key = trim((string) ($row['key'] ?? ''));
                $label = trim((string) ($row['label'] ?? ''));
                $value = (string) ($row['value'] ?? '');
                if ($key === '' || $label === '' || $value === '') {
                    throw new ExceptionBusiness('字典项缺少 key、label 或 value：' . $name);
                }
                // 已存在的常量沿用库中数据，避免覆盖管理员调整过的标签与样式
                $values[] = $exists[$key] ?? [
                    'key' => $key,
                    'label' => $label,
                    'value' => $value,
                    'style' => (string) ($row['style'] ?? ''),
                ];
                $count++;
            }
            $data = [
                'plugin' => $plugin,
                'name' => $name,
                'title' => $title,
                'values' => static::encodeValues($values),
                'is_system' => YesEnum::YES['value'],
                'sort' => $index,
            ];
            if ($model) {
                // 已存在：枚举标题与排序归后台管理，保留库中调整过的值
                $data['title'] = (string) $model['title'] !== '' ? (string) $model['title'] : $title;
                $data['sort'] = (int) $model['sort'];
                $model->save($data);
            } else {
                $model = new static;
                $model->save($data);
            }
        }
        return $count;
    }
}