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
namespace plugin\xbAdmin\app\validate;

use Closure;
use Webman\Validation\Validator;
use plugin\xbAdmin\enum\CronPresetsEnum;

/**
 * 定时任务验证器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class CrontabValidate extends Validator
{
    /**
     * 验证规则
     * @var array
     */
    protected array $rules = [
        'title' => 'required',
        'name' => 'required',
        'plugin' => 'required',
        'type' => 'required',
        'cron_expression' => 'required',
        'command' => 'required',
    ];

    /**
     * 错误提示
     * @var array
     */
    protected array $messages = [
        'title.required' => '请填写定时任务名称',
        'name.required' => '请填写定时任务标识',
        'plugin.required' => '请填写插件名称',
        'type.required' => '请填写定时任务类型',
        'cron_expression.required' => '请选择或输入执行周期',
        'command.required' => '请填写定时任务命令',
    ];

    /**
     * 验证场景
     * @var array
     */
    protected array $scenes = [
        'add' => ['title', 'name', 'plugin', 'type', 'cron_expression', 'command'],
        'edit' => ['title', 'name', 'plugin', 'type', 'cron_expression', 'command'],
    ];

    /**
     * 构建验证规则
     * Laravel 验证器无法调用子类自定义方法，此处以闭包规则完成 Cron 表达式校验
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function rules(): array
    {
        $rules = parent::rules();
        if (array_key_exists('cron_expression', $rules)) {
            $rules['cron_expression'] = ['required', $this->cronExpressionRule()];
        }
        return $rules;
    }

    /**
     * Cron表达式校验闭包
     * @return Closure
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function cronExpressionRule(): Closure
    {
        return function ($attribute, $value, $fail) {
            // 如果是预设选项，直接通过
            $presetValues = array_column(CronPresetsEnum::options(), 'value');
            if (in_array($value, $presetValues)) {
                return;
            }
            // 验证自定义cron表达式格式
            // 标准cron表达式格式: 分 时 日 月 周 (5位) 或 秒 分 时 日 月 周 (6位)
            $pattern = '/^(\*|(\*\/)?[0-9]+([,\/\-][0-9]+)*)\s+(\*|(\*\/)?[0-9]+([,\/\-][0-9]+)*)\s+(\*|(\*\/)?[0-9]+([,\/\-][0-9]+)*)\s+(\*|(\*\/)?[0-9]+([,\/\-][0-9]+)*)\s+(\*|(\*\/)?[0-9]+([,\/\-][0-9]+)*)(\s+(\*|(\*\/)?[0-9]+([,\/\-][0-9]+)*))?$/';
            if (!preg_match($pattern, trim((string) $value))) {
                $fail('Cron表达式格式错误，请检查格式是否正确');
                return;
            }
            // 检查是否有 */0 这样的无效表达式
            if (preg_match('/\*\/0/', (string) $value)) {
                $fail('Cron表达式格式错误，请检查格式是否正确');
            }
        };
    }
}
