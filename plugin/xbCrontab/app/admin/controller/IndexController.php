<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xhadmin.cn
 * @document http://doc.xhadmin.cn
 */
namespace plugin\xbCrontab\app\admin\controller;

use Tinywan\Jwt\JwtToken;
use plugin\xbCode\api\Url;
use plugin\xbCode\XbController;
use plugin\xbCode\api\PluginsApi;
use plugin\xbCrontab\enum\MonthEnum;
use plugin\xbCrontab\api\CrontabApi;
use plugin\xbCrontab\enum\StateEnum;
use plugin\xbCrontab\enum\WeekdayEnum;
use plugin\xbCrontab\app\model\Crontab;
use plugin\xbCrontab\enum\TaskTypeEnum;
use plugin\xbCrontab\enum\CronCycleEnum;
use plugin\xbCode\builder\Renders\XbCrud;
use plugin\xbCode\builder\Renders\XbForm;
use plugin\xbCrontab\app\model\CrontabLog;
use plugin\xbCrontab\api\CronExpressionApi;
use plugin\xbCode\exception\business\ExceptionBadRequest;

/**
 * 定时任务控制器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class IndexController extends XbController
{
    /**
     * 列表
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function index()
    {
        if (request()->get('_act')) {
            $builder = XbCrud::make();
            // 头部
            $builder->addHeaderDialog('添加', Url::make('add'), [
                'title' => '添加定时任务',
            ]);
            // 表列
            $builder->addColumn('title', '任务名称');
            $builder->addColumn('plugin', '所属插件');
            $builder->addColumnMap('type', '任务类型', TaskTypeEnum::dict())->width(130);
            $builder->addColumn('cron_desc', '执行周期');
            $html = <<<HTML
            <span class="badge badge-pill badge-info">\${log_num}条</span>
            HTML;
            $builder->addColumnTpl('log_num', '日志数量', $html)
                ->width(120)->align('center');
            $builder->addColumnMap('state', '任务状态', StateEnum::dict())
                ->width(100);
            $builder->addColumn('last_time', '最后执行')
                ->width(150);
            $builder->addColumn('error', '失败原因')
                ->width(150);
            $builder->addColumn('create_at', '创建时间')
                ->width(150)
                ->align('center');
            // 操作
            $builder->setActionConfig('width', 360);
            $builder->addRightActionConfirm('立即启动', Url::make('run'), [
                'content' => '是否立即执行该定时任务？',
            ])->success(true);
            $builder->addRightActionConfirm('执行导出', Url::make('export'), [
                'content' => '是否确认导出该定时任务？',
            ])->success(true);
            $builder->addRightActionDialog('日志管理', Url::make('CrontabLog/index')->query([
                '_act' => 'ajax',
            ]), [
                'title' => '定时任务日志',
                'size' => 'lg',
            ])->cancelActions()->warning(true);
            $builder->addRightActionDialog('任务设置', Url::make('edit'), [
                'title' => '定时任务设置',
            ]);
            $builder->addRightActionConfirm('立即删除', Url::make('del'), [
                'content' => '是否确认删除该定时任务？',
            ]);
            return $this->successRes($builder);
        }
        $keyword = request()->get('keyword');
        $where = [];
        if ($keyword) {
            $where[] = ['title', 'like', "%{$keyword}%"];
        }
        $data = Crontab::where($where)
            ->paginate()
            ->each(function ($item) {
                // 查询日志数量
                $count = CrontabLog::where('crontab_id', $item->id)->count();
                $item->log_num = $count;
                $item->run_second_time = "{$item->run_second_time} 秒";
            });
        return $this->successData($data);
    }

    /**
     * 添加
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function add()
    {
        if (request()->method() === 'POST') {
            $post = request()->post();
            $this->processCronParams($post);
            $this->checkTaskTypeAuth($post);
            CrontabApi::make()->add($post);
            return $this->success('添加成功');
        }
        $builder = $this->formView();
        $builder->setSaveMethod('POST');
        return $this->successRes($builder);
    }

    /**
     * 编辑
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function edit()
    {
        if (request()->method() === 'PUT') {
            $id = (int) request()->get('id');
            $post = request()->post();
            $this->processCronParams($post);
            $this->checkTaskTypeAuth($post);
            CrontabApi::make()->edit($id, $post);
            return $this->success('编辑成功');
        }
        $builder = $this->formView();
        $builder->setSaveMethod('PUT');
        // 获取数据并转换周期参数
        $id = (int) request()->get('id');
        $model = Crontab::find($id);
        if ($model) {
            $formData = $model->toArray();
            // 解析cron表达式为周期参数
            $cronParams = CronExpressionApi::make()->parseCronToParams($formData['cron_expression'] ?? '');
            $formData['cycle_type'] = $cronParams['cycle_type'];
            $formData['cycle_time'] = $cronParams['time'];
            // 根据周期类型设置对应的参数字段
            switch ($cronParams['cycle_type']) {
                case 'second':
                    $formData['cycle_second_interval'] = $cronParams['interval'];
                    break;
                case 'minute':
                    $formData['cycle_minute_interval'] = $cronParams['interval'];
                    break;
                case 'hour':
                    $formData['cycle_hour_interval'] = $cronParams['interval'];
                    break;
                case 'week':
                    $formData['cycle_week_weekday'] = $cronParams['weekday'];
                    break;
                case 'month':
                    $formData['cycle_month_day'] = $cronParams['interval'];
                    break;
                case 'year':
                    $formData['cycle_year_month'] = $cronParams['interval'];
                    $formData['cycle_year_day'] = $cronParams['day'] ?? 1;
                    break;
            }
            $builder->setData($formData);
        }
        return $this->successRes($builder);
    }

    /**
     * 处理Cron参数
     * @param array $post
     * @return void
     */
    private function processCronParams(array &$post): void
    {
        $cycleType = $post['cycle_type'] ?? 'minute';
        $time = $post['cycle_time'] ?? '00:00';

        // 根据周期类型获取对应参数
        $interval = 1;
        $weekday = 0;

        switch ($cycleType) {
            case 'second':
                $interval = intval($post['cycle_second_interval'] ?? 1);
                break;
            case 'minute':
                $interval = intval($post['cycle_minute_interval'] ?? 1);
                break;
            case 'hour':
                $interval = intval($post['cycle_hour_interval'] ?? 1);
                break;
            case 'week':
                $weekday = intval($post['cycle_week_weekday'] ?? 0);
                $interval = $weekday;
                break;
            case 'month':
                $interval = intval($post['cycle_month_day'] ?? 1);
                break;
            case 'year':
                $interval = intval($post['cycle_year_month'] ?? 1);
                $yearDay = intval($post['cycle_year_day'] ?? 1);
                break;
        }

        $result = CronExpressionApi::make()->buildCronExpression($cycleType, $interval, $time, $weekday, $yearDay ?? 1);
        $post['cron_expression'] = $result['expression'];
        $post['cron_desc'] = $result['desc'];
    }

    /**
     * 校验任务类型操作权限
     * Shell命令、PHP代码属于高危执行能力，仅允许系统管理员创建、编辑或删除
     * @param array $post 任务数据（含 type 字段）
     * @throws ExceptionBadRequest
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function checkTaskTypeAuth(array $post): void
    {
        // 仅高危任务类型需要校验
        if (!in_array((string) ($post['type'] ?? ''), ['10', '30'], true)) {
            return;
        }
        // 获取当前登录管理员
        $user = JwtToken::getExtend();
        // 仅系统管理员可操作
        if (($user['is_system'] ?? '') !== '20') {
            throw new ExceptionBadRequest('仅系统管理员可操作 Shell命令、PHP代码类型任务');
        }
    }

    /**
     * 删除
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function del()
    {
        $id = (int) request()->get('id');
        $model = Crontab::find($id);
        if (!$model) {
            throw new ExceptionBadRequest('定时任务不存在');
        }
        $this->checkTaskTypeAuth($model->toArray());
        CrontabApi::make()->del($id);
        return $this->success('删除成功');
    }

    /**
     * 立即执行一次定时任务
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function run()
    {
        $id = (int) request()->get('id');
        $model = Crontab::find($id);
        if (!$model) {
            throw new ExceptionBadRequest('定时任务不存在');
        }
        CrontabApi::make()->run($model);
        return $this->success('任务执行成功');
    }

    /**
     * 导出定时任务
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function export()
    {
        $id = (int) request()->get('id');
        CrontabApi::make()->exportCrontab([$id]);
        return $this->success('导出定时任务成功');
    }

    /**
     * 表单视图
     * @return XbForm
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function formView()
    {
        $builder = XbForm::make();
        $plugins = PluginsApi::make()->options();

        // 基础信息
        $builder->addRowGroup('basic', [
            $builder->addRowInput('title', '任务名称')
                ->description('示例：清除订单过期数据'),
            $builder->addRowSelect('plugin', '所属插件')
                ->description('选择执行的插件任务')
                ->options($plugins),
        ]);

        // 任务类型
        $builder->addRowGroup('type', [
            $builder->addRowSelect('type', '任务类型')
                ->description('选择任务类型')
                ->options(TaskTypeEnum::options()),
            $builder->addRowInput('name', '任务标识')
                ->description('该插件旗下唯一标识(必须唯一)'),
        ]);

        // 执行周期
        $builder->addRowGroup('cron', [
            $builder->addRowSelect('cycle_type', '周期类型')
                ->options(CronCycleEnum::options())
                ->value('minute'),
            // 每秒
            $builder->addRowInput('cycle_second_interval', '间隔值')
                ->visibleOn('this.cycle_type == "second"')
                ->placeholder('输入数字')
                ->description('间隔秒数(1-59)，如: 5'),
            // 每分钟
            $builder->addRowInput('cycle_minute_interval', '间隔值')
                ->visibleOn('this.cycle_type == "minute"')
                ->placeholder('输入数字')
                ->description('间隔数量，如: 5'),
            // 每小时
            $builder->addRowInput('cycle_hour_interval', '间隔值')
                ->visibleOn('this.cycle_type == "hour"')
                ->placeholder('输入数字')
                ->description('间隔数量，如: 5'),
            // 每周
            $builder->addRowSelect('cycle_week_weekday', '星期几')
                ->visibleOn('this.cycle_type == "week"')
                ->options(WeekdayEnum::options()),
            // 每月几号
            $builder->addRowInput('cycle_month_day', '几号')
                ->visibleOn('this.cycle_type == "month"')
                ->placeholder('1-31')
                ->description('每月几号执行，如: 15'),
            // 每年：月份+几号+执行时间
            $builder->addRowSelect('cycle_year_month', '月份')
                ->visibleOn('this.cycle_type == "year"')
                ->options(MonthEnum::options()),
            $builder->addRowInput('cycle_year_day', '几号')
                ->visibleOn('this.cycle_type == "year"')
                ->placeholder('1-31')
                ->description('每年几号执行，如: 1'),
            // 执行时间：每天、每周、每月、每年
            $builder->addRowInput('cycle_time', '执行时间')
                ->visibleOn('this.cycle_type == "day" || this.cycle_type == "week" || this.cycle_type == "month" || this.cycle_type == "year"')
                ->placeholder('HH:MM')
                ->value('00:00')
                ->description('24小时制，如: 02:30'),
            // 隐藏字段存储最终表达式和描述
            $builder->addRowHidden('cron_expression')->value(''),
            $builder->addRowHidden('cron_desc')->value(''),
        ]);

        // 任务状态
        $builder->addRowGroup('state', [
            $builder->addRowRadioButton('state', '任务状态', '20')
                ->description('选择任务状态')
                ->options(StateEnum::options()),
        ]);

        // 任务命令
        $commandDesc = <<<HTML
            <div style="display:flex;flex-direction:column;gap:5px;">
                <b style="color:#000;">根据任务类型编写执行命令</b>
                <div>
                    Shell命令示例：php webman plugin:xbOrder:clearExpire
                </div>
                <div>
                    <p>URL请求示例：http://localhost/app/xbOrder/clearExpire</p>
                    <p style="color:red;">注意：URL请求示例中的localhost需要替换为实际域名或IP地址</p>
                </div>
                <div>
                    PHP代码示例：
                    echo "你好，xbCode";
                </div>
            </div>
            HTML;
        $builder->addRowTextarea('command', '任务命令')
            ->description($commandDesc);
        return $builder;
    }
}
