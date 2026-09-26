<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\app\model;

use plugin\xbCode\api\Setting;
use plugin\xbCode\app\Model;
use plugin\xbAdmin\exception\business\ExceptionBusiness;

/**
 * 系统配置模型
 *
 * 一条记录以 plugin + group + name 唯一标识，value 统一按文本存储：
 * 数组、布尔等非字符串值编码为 JSON，读取时还原。
 *
 * 分组模板来自 xb_config_group，由插件安装时经 HTTP 上报写入，后台只读该表，
 * 因此后台与插件可以分服务器部署；本表只保存管理员填写的字段值。
 */
class Config extends Model
{
    /**
     * 读取配置分组记录，按 sort asc, id asc 排序
     * @return array 每项含 plugin / group / title / body / sort
     */
    public static function entries(): array
    {
        return ConfigGroup::order('sort asc,id asc')->select()->toArray();
    }

    /**
     * 读取分组字段模板
     * @param string $plugin 插件标识
     * @param string $group 分组标识，即 setting 目录下的文件名
     * @return array
     */
    public static function template(string $plugin, string $group): array
    {
        $body = ConfigGroup::where([
            'plugin' => $plugin,
            'group' => $group,
        ])->value('body');
        if (!is_string($body) || $body === '') {
            return [];
        }
        $components = json_decode($body, true);
        return is_array($components) ? $components : [];
    }

    /**
     * 读取所有配置选项卡
     *
     * name 以「插件标识_分组标识」拼接，保证多插件分组重名时选项卡键唯一。
     * @return array [['name' => ..., 'title' => ..., 'body' => 组件], ...]
     */
    public static function tabs(): array
    {
        $tabs = [];
        foreach (static::entries() as $entry) {
            $plugin = (string) $entry['plugin'];
            $group = (string) $entry['group'];
            $tabs[] = [
                'name' => $plugin . '_' . $group,
                'title' => (string) $entry['title'] !== '' ? (string) $entry['title'] : $group,
                'body' => static::template($plugin, $group),
            ];
        }
        return $tabs;
    }

    /**
     * 读取分组字段组件，键为字段名
     * @param string $plugin 插件标识
     * @param string $group 分组标识
     * @return array
     */
    public static function fields(string $plugin, string $group): array
    {
        return Setting::fields(static::template($plugin, $group));
    }

    /**
     * 读取分组字段值：模板默认值 + 库中已保存值
     * @param string $plugin 插件标识
     * @param string $group 分组标识
     * @return array 键为字段名
     */
    public static function groupData(string $plugin, string $group): array
    {
        $data = [];
        foreach (static::fields($plugin, $group) as $name => $component) {
            $data[$name] = static::decode($component['value'] ?? '');
        }
        $rows = static::where('plugin', $plugin)->where('group', $group)->column('value', 'name');
        foreach ($rows as $name => $value) {
            $name = (string) $name;
            if (array_key_exists($name, $data)) {
                $data[$name] = static::decode($value);
            }
        }
        return $data;
    }

    /**
     * 读取所有分组字段值，用于选项卡表单回填
     * @return array 键为字段名
     */
    public static function allData(): array
    {
        $data = [];
        foreach (static::entries() as $entry) {
            $data = array_merge($data, static::groupData((string) $entry['plugin'], (string) $entry['group']));
        }
        return $data;
    }

    /**
     * 保存分组字段值
     *
     * 只接受模板中声明的字段，未提交的字段保持原值；已存在则更新，不存在则插入。
     * @param string $plugin 插件标识
     * @param string $group 分组标识
     * @param array $data 待保存数据，键为字段名
     * @return void
     */
    public static function saveGroup(string $plugin, string $group, array $data): void
    {
        foreach (array_keys(static::fields($plugin, $group)) as $name) {
            if (!array_key_exists($name, $data)) {
                continue;
            }
            $value = static::encode($data[$name]);
            $model = static::where('plugin', $plugin)->where('group', $group)->where('name', $name)->find();
            if ($model) {
                $model->save(['value' => $value]);
                continue;
            }
            $model = new static;
            $model->save(['plugin' => $plugin, 'group' => $group, 'name' => $name, 'value' => $value]);
        }
    }

    /**
     * 保存所有分组字段值
     *
     * 命名避开 think\Model::saveAll()，两者的语义与签名不同。
     * @param array $data 待保存数据，键为字段名
     * @return void
     */
    public static function saveAllGroups(array $data): void
    {
        foreach (static::entries() as $entry) {
            static::saveGroup((string) $entry['plugin'], (string) $entry['group'], $data);
        }
    }

    /**
     * 注册插件上报的配置分组
     *
     * 分组模板按「插件 + 分组」幂等覆盖；字段值只在缺失时写入，避免插件重复安装覆盖管理员已改的配置。
     * @param string $plugin 插件标识
     * @param array $groups 上报的分组数据，每项含 group / title / sort / body
     * @throws ExceptionBusiness
     * @return int 本次写入的字段值条数
     */
    public static function register(string $plugin, array $groups): int
    {
        $count = 0;
        foreach ($groups as $item) {
            if (!is_array($item)) {
                throw new ExceptionBusiness('配置分组数据格式错误');
            }
            $group = trim((string) ($item['group'] ?? ''));
            if (!preg_match('/^[\w\-]+$/', $group)) {
                throw new ExceptionBusiness('配置分组标识错误');
            }
            $body = $item['body'] ?? [];
            if (!is_array($body) || !$body) {
                throw new ExceptionBusiness('配置分组模板不能为空：' . $group);
            }
            $model = ConfigGroup::where([
                'plugin' => $plugin,
                'group' => $group,
            ])->find();
            if (!$model) {
                $model = new ConfigGroup;
            }
            $model->save([
                'plugin' => $plugin,
                'group' => $group,
                'title' => trim((string) ($item['title'] ?? '')),
                'body' => (string) json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'sort' => (int) ($item['sort'] ?? 0),
            ]);
            $count += static::initFields($plugin, $group, $body);
        }
        return $count;
    }

    /**
     * 写入分组字段的初始值，已存在的字段不覆盖
     * @param string $plugin 插件标识
     * @param string $group 分组标识
     * @param array $body 分组模板组件
     * @return int 写入条数
     */
    protected static function initFields(string $plugin, string $group, array $body): int
    {
        $count = 0;
        foreach (Setting::fields($body) as $name => $component) {
            $exists = static::where('plugin', $plugin)->where('group', $group)->where('name', $name)->find();
            if ($exists) {
                continue;
            }
            $model = new static;
            $model->save([
                'plugin' => $plugin,
                'group' => $group,
                'name' => $name,
                'value' => static::encode($component['value'] ?? ''),
            ]);
            $count++;
        }
        return $count;
    }

    /**
     * 配置值编码
     *
     * 数组、布尔等非文本值编码为 JSON 文本，普通文本原样存储，保证表数据可读。
     * @param mixed $value
     * @return string
     */
    protected static function encode(mixed $value): string
    {
        if (is_array($value) || is_object($value) || is_bool($value)) {
            return (string) json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        return (string) $value;
    }

    /**
     * 配置值解码
     *
     * 只还原数组与布尔，普通文本原样返回，避免 "123"、"true" 这类文本被误判成数字或布尔。
     * @param mixed $value
     * @return mixed
     */
    protected static function decode(mixed $value): mixed
    {
        if (!is_string($value) || $value === '') {
            return $value ?? '';
        }
        $decoded = json_decode($value, true);
        return is_array($decoded) || is_bool($decoded) ? $decoded : $value;
    }
}