<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\app\model;

use plugin\xbCode\app\Model;
use plugin\xbAdmin\enum\PeriodEnum;
use plugin\xbAdmin\enum\ExecuteModeEnum;

/**
 * 定时任务模型
 *
 * mode 存执行方式：php（默认）用 target、params 两个字段；command 用 type、command 两个字段。
 * target 存 PHP 可调用目标（类名::方法名），params 存方法参数的 JSON 数组文本；
 * type 存任务类型（10 Shell命令 / 20 访问URL / 30 PHP代码），command 存对应的命令文本。
 *
 * rule 存「执行周期」配置的 JSON，描述一个固定间隔，形如
 * {"period":"day","day":1,"hour":3,"minute":30,"second":0}，含义是每 1 天 3 时 30 分执行一次。
 * 各档位只记录自己用到的单位，单位换算见 UNITS、UNIT_SECONDS：
 * 周按 7 天、月按 30 天折算为固定时长，因此周期是「固定间隔」而非日历对齐。
 * 后台表单按所选档位逐级填写各单位数值，由 buildRule() 合成 rule，
 * 编辑时再由 parseRule() 反解回填到各单位字段；调度进程用 toSeconds() 换算成秒后交由定时器。
 *
 * 列表展示用 rule_text 虚拟属性输出可读文案（如「每1天3时30分」），由 getRuleTextAttr() 计算。
 */
class Crontab extends Model
{
    /**
     * 各档位用到的单位及其取值上限，按「大单位 → 小单位」排列
     *
     * 首位单位是最小计数单位（必须大于 0），其后为余数单位（可为 0）。
     * 上层单位已经能表达该量级，故各余数单位的上限小于其上级的换算比：
     * 秒/分 0-59、时 0-23、日 0-6（周内）或 0-30（月内）。
     */
    protected const UNITS = [
        'second' => ['second' => 59],
        'minute' => ['minute' => 59, 'second' => 59],
        'hour' => ['hour' => 23, 'minute' => 59, 'second' => 59],
        'day' => ['day' => 30, 'hour' => 23, 'minute' => 59, 'second' => 59],
        'week' => ['week' => 999, 'day' => 6, 'hour' => 23, 'minute' => 59, 'second' => 59],
        'month' => ['month' => 999, 'day' => 30, 'hour' => 23, 'minute' => 59, 'second' => 59],
    ];

    /**
     * 各单位换算为秒（周按 7 天、月按 30 天）
     */
    protected const UNIT_SECONDS = [
        'second' => 1,
        'minute' => 60,
        'hour' => 3600,
        'day' => 86400,
        'week' => 604800,
        'month' => 2592000,
    ];

    /**
     * 首位单位的中文名
     */
    protected const UNIT_LABELS = [
        'second' => '秒',
        'minute' => '分',
        'hour' => '小时',
        'day' => '天',
        'week' => '周',
        'month' => '月',
    ];

    /**
     * 余数单位的中文名
     */
    protected const INNER_LABELS = [
        'second' => '秒',
        'minute' => '分',
        'hour' => '时',
        'day' => '日',
    ];

    /**
     * 输出附加属性，供列表直接展示可读的执行周期与执行内容
     * @return array
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getOptions(): array
    {
        return ['append' => ['rule_text', 'content_text']];
    }

    /**
     * 列表展示的执行内容
     *
     * content_text 不在数据表内，取值时按执行方式返回对应字段：
     * php 方式取执行目标，command 方式取任务命令。
     * @param mixed $value 恒为 null，该字段不落库
     * @param array $data 模型原始数据
     * @return string
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getContentTextAttr($value, array $data = []): string
    {
        if ((string) ($data['mode'] ?? '') === ExecuteModeEnum::COMMAND['value']) {
            return (string) ($data['command'] ?? '');
        }
        return (string) ($data['target'] ?? '');
    }

    /**
     * 可读的执行周期文案
     *
     * rule_text 不在数据表内，取值时由本方法按 rule 计算；末尾为 0 的余数单位不展示。
     * @param mixed $value 恒为 null，该字段不落库
     * @param array $data 模型原始数据
     * @return string
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getRuleTextAttr($value, array $data = []): string
    {
        return static::makeRuleText(static::parseRule((string) ($data['rule'] ?? '')));
    }

    /**
     * 设置执行参数
     *
     * 入库前把参数数组编码成 JSON 文本，空数组存空字符串；
     * 不定义读取器，读取时保持原始 JSON 文本，便于表单回填与进程解析。
     * @param mixed $value
     * @return string
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function setParamsAttr($value)
    {
        if (is_array($value)) {
            $value = array_values($value);
            return $value ? (string) json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '';
        }
        return $value;
    }

    /**
     * 解析执行参数
     * @param string $json JSON 数组文本
     * @return array
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function parseParams(string $json): array
    {
        $params = json_decode(trim($json), true);
        return is_array($params) ? array_values($params) : [];
    }

    /**
     * 按表单提交的周期配置合成 rule
     *
     * 只记录该档位用到的单位，数值按各单位上限裁剪；首位单位为 0 时按 1 处理，
     * 保证间隔至少 1 秒（「每 0 秒」无意义）。
     * @param array $post 表单数据，含 period 及各单位字段
     * @return string 周期配置 JSON，档位非法时返回空字符串
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function buildRule(array $post): string
    {
        $period = (string) ($post['period'] ?? '');
        if (!isset(static::UNITS[$period])) {
            return '';
        }
        $data = ['period' => $period];
        foreach (static::UNITS[$period] as $unit => $max) {
            $value = max(0, min($max, (int) ($post[$unit] ?? 0)));
            // 首位单位是间隔的最小计数单位，为 0 时取 1
            if ($data === ['period' => $period] && $value < 1) {
                $value = 1;
            }
            $data[$unit] = $value;
        }
        return (string) json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * 反解 rule，得到表单各单位字段的值
     *
     * 识别不出的 rule（历史数据、非法 JSON）按默认周期返回，保证表单可正常回填。
     * @param string $rule 已入库的周期配置
     * @return array 含 period 及 month/week/day/hour/minute/second
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function parseRule(string $rule): array
    {
        $rule = trim($rule);
        $config = $rule === '' ? null : json_decode($rule, true);
        $period = is_array($config) ? (string) ($config['period'] ?? '') : '';
        if (!isset(static::UNITS[$period])) {
            $period = PeriodEnum::MINUTE['value'];
            $config = null;
        }
        $data = [
            'period' => $period,
            'month' => 1,
            'week' => 1,
            'day' => 1,
            'hour' => 0,
            'minute' => 1,
            'second' => 0,
        ];
        foreach (static::UNITS[$period] as $unit => $max) {
            $data[$unit] = max(0, min($max, (int) ($config[$unit] ?? 0)));
        }
        // 首位单位为 0 的脏数据按 1 处理，与 buildRule 保持一致
        $first = array_key_first(static::UNITS[$period]);
        $data[$first] = max(1, $data[$first]);
        return $data;
    }

    /**
     * 把周期配置换算为间隔秒数
     * @param array $config parseRule() 或 buildRule() 解码后的周期配置
     * @return int 间隔秒数，档位非法时为 0
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function toSeconds(array $config): int
    {
        $period = (string) ($config['period'] ?? '');
        if (!isset(static::UNITS[$period])) {
            return 0;
        }
        $seconds = 0;
        foreach (static::UNITS[$period] as $unit => $max) {
            $value = max(0, min($max, (int) ($config[$unit] ?? 0)));
            $seconds += $value * static::UNIT_SECONDS[$unit];
        }
        return $seconds;
    }

    /**
     * 把周期配置转成可读文案
     *
     * 方法名不能是 ruleText：think-orm 读取虚拟属性前会按关联处理，把 rule_text
     * 驼峰化后调用同名方法当作关联查询，会与本方法冲突。
     * @param array $config parseRule() 或 buildRule() 解码后的周期配置
     * @return string 形如「每1天3时30分」，档位非法时为空字符串
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function makeRuleText(array $config): string
    {
        $period = (string) ($config['period'] ?? '');
        if (!isset(static::UNITS[$period])) {
            return '';
        }
        $values = [];
        $labels = [];
        foreach (static::UNITS[$period] as $unit => $max) {
            // 首位单位用「天/小时」这类量词，其余用「日/时」这类衔接词
            $labels[] = $values ? static::INNER_LABELS[$unit] : static::UNIT_LABELS[$unit];
            $values[] = max(0, min($max, (int) ($config[$unit] ?? 0)));
        }
        // 末尾为 0 的余数单位不展示，至少保留首位单位
        while (count($values) > 1 && $values[count($values) - 1] === 0) {
            array_pop($values);
            array_pop($labels);
        }
        $text = '';
        foreach ($values as $index => $value) {
            $text .= $value . $labels[$index];
        }
        return '每' . $text;
    }
}