<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xhadmin.cn
 * @document http://doc.xhadmin.cn
 */
namespace plugin\xbAdmin\app\admin\controller;

use support\Request;
use support\Response;
use plugin\xbAdmin\api\Url;
use plugin\xbAdmin\enum\MonthEnum;
use plugin\xbAdmin\enum\WeekdayEnum;
use plugin\xbAdmin\enum\TaskTypeEnum;
use plugin\xbAdmin\api\PluginsApi;
use plugin\xbCode\api\Gateway;
use plugin\xbAdmin\enum\CronCycleEnum;
use plugin\xbAdmin\enum\CrontabStateEnum;
use plugin\xbAdmin\app\BaseController;
use plugin\xbCode\builder\Renders\XbCrud;
use plugin\xbCode\builder\Renders\XbForm;

/**
 * 定时任务控制器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class CrontabController extends BaseController
{
    /**
     * 列表
     * @param Request $request
     * @return Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function index(Request $request)
    {
        if (!$request->get('_act')) {
            $result = Gateway::get('xbAdmin/api/Crontab/index', $request->get());
            return $this->response($result);
        }
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
        $builder->addColumnMap('state', '任务状态', CrontabStateEnum::dict())
            ->width(100);
        $builder->addColumn('last_time', '最后执行')
            ->width(150);
        $builder->addColumn('error', '失败原因')
            ->width(150);
        $builder->addColumn('create_at', '创建时间')
            ->width(150)
            ->align('center');
        // 操作
        $builder->addRightActionConfirm('执行导出', Url::make('export'), [
            'content' => '是否确认导出该定时任务？',
        ])->className('text-success');
        $builder->addRightActionDialog('日志管理', Url::make('CrontabLog/index'), [
            'title' => '定时任务日志',
            'size' => 'lg',
            'actions' => [],
        ])->className('text-warning');
        $builder->addRightActionDialog('点击设置', Url::make('edit'), [
            'title' => '定时任务设置',
        ]);
        $builder->addRightActionConfirm('立即删除', Url::make('del'), [
            'content' => '是否确认删除该定时任务？',
        ]);
        return $this->successRes($builder);
    }

    /**
     * 添加
     * @param Request $request
     * @return Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function add(Request $request)
    {
        if ($request->method() === 'POST') {
            $result = Gateway::post('xbAdmin/api/Crontab/add', (array) $request->post());
            return $this->response($result);
        }
        $builder = $this->formView();
        $builder->setSaveMethod('POST');
        return $this->successRes($builder);
    }

    /**
     * 编辑
     * @param Request $request
     * @return Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function edit(Request $request)
    {
        $id = (int) $request->get('id');
        if ($request->method() === 'PUT') {
            $result = Gateway::put("xbAdmin/api/Crontab/edit?id={$id}", (array) $request->post());
            return $this->response($result);
        }
        $builder = $this->formView();
        $builder->setSaveMethod('PUT');
        $builder->setData($this->findModel($id));
        return $this->successRes($builder);
    }

    /**
     * 删除
     * @param Request $request
     * @return Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function del(Request $request)
    {
        $id = (int) $request->get('id');
        $result = Gateway::delete("xbAdmin/api/Crontab/del?id={$id}");
        return $this->response($result);
    }

    /**
     * 导出定时任务
     * @param Request $request
     * @return Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function export(Request $request)
    {
        $id = (int) $request->get('id');
        $result = Gateway::get("xbAdmin/api/Crontab/export?id={$id}");
        return $this->response($result);
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
                ->options(CrontabStateEnum::options()),
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

    /**
     * 按ID查找定时任务（含周期参数回填）
     * @param int $id
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function findModel(int $id): array
    {
        return Gateway::get("xbAdmin/api/Crontab/find?id={$id}")['data'] ?? [];
    }
}