<?php
/**
 * 贵州积木云网络科技有限公司
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xhadmin.cn
 * @document http://doc.xhadmin.cn
 */
namespace plugin\xbCrontab\app\validate;

use taoser\Validate;
use plugin\xbCrontab\enum\CronPresetsEnum;

/**
 * 定时任务验证器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class CrontabValidate extends Validate
{
    protected $rule = [
        'title' => 'require',
        'name' => 'require',
        'plugin' => 'require',
        'type' => 'require',
        'cron_expression' => 'require|checkCronExpression',
        'command' => 'require',
    ];

    protected $message = [
        'title.require' => '请填写定时任务名称',
        'name.require' => '请填写定时任务标识',
        'plugin.require' => '请填写插件名称',
        'type.require' => '请填写定时任务类型',
        'cron_expression.require' => '请选择或输入执行周期',
        'command.require' => '请填写定时任务命令',
    ];

    /**
     * 检查Cron表达式是否正确
     * @param mixed $value
     * @param mixed $rule
     * @param mixed $data
     * @return bool|string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function checkCronExpression($value, $rule, $data)
    {
        // 如果是预设选项，直接通过
        $presetValues = array_column(CronPresetsEnum::options(), 'value');
        if (in_array($value, $presetValues)) {
            return true;
        }
        // 验证自定义cron表达式格式
        // 标准cron表达式格式: 分 时 日 月 周 (5位) 或 秒 分 时 日 月 周 (6位)
        $pattern = '/^(\*|(\*\/)?[0-9]+([,\/\-][0-9]+)*)\s+(\*|(\*\/)?[0-9]+([,\/\-][0-9]+)*)\s+(\*|(\*\/)?[0-9]+([,\/\-][0-9]+)*)\s+(\*|(\*\/)?[0-9]+([,\/\-][0-9]+)*)\s+(\*|(\*\/)?[0-9]+([,\/\-][0-9]+)*)(\s+(\*|(\*\/)?[0-9]+([,\/\-][0-9]+)*))?$/';
        if (!preg_match($pattern, trim($value))) {
            return 'Cron表达式格式错误，请检查格式是否正确';
        }
        // 检查是否有 */0 这样的无效表达式
        if (preg_match('/\*\/0/', $value)) {
            return 'Cron表达式格式错误，请检查格式是否正确';
        }
        // 检查标准5位格式的每天表达式 (如 0 0 * * *)
        // 这种格式：固定时间 + * * * 表示每天
        $parts = preg_split('/\s+/', trim($value));
        if (count($parts) === 5) {
            // 如果第3、4、5位都是*，则是每天一次的表达式
            if ($parts[2] === '*' && $parts[3] === '*' && $parts[4] === '*') {
                return true;
            }
        }
        return true;
    }
}
