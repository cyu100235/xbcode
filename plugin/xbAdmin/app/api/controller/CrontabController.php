<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbAdmin\app\api\controller;

use support\Request;
use support\Response;
use plugin\xbAdmin\api\CrontabApi;
use plugin\xbAdmin\app\model\Crontab;
use plugin\xbAdmin\app\BaseController;
use plugin\xbAdmin\app\model\CrontabLog;
use plugin\xbAdmin\api\CronExpressionApi;

/**
 * 定时任务接口
 * @copyright 贵州云铺网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class CrontabController extends BaseController
{
    /**
     * 列表
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $keyword = $request->get('keyword');
        $where = [];
        if ($keyword) {
            $where[] = ['title', 'like', "%{$keyword}%"];
        }
        $data = Crontab::where($where)
            ->paginate()
            ->each(function ($item) {
                // 查询日志数量
                $item->log_num = CrontabLog::where('crontab_id', $item->id)->count();
            });
        return $this->successData($data);
    }

    /**
     * 添加
     * @param Request $request
     * @return Response
     */
    public function add(Request $request)
    {
        $post = (array) $request->post();
        $this->processCronParams($post);
        CrontabApi::make()->add($post);
        return $this->success('添加成功');
    }

    /**
     * 编辑
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request)
    {
        $id = (int) $request->get('id');
        $post = (array) $request->post();
        $this->processCronParams($post);
        CrontabApi::make()->edit($id, $post);
        return $this->success('编辑成功');
    }

    /**
     * 查询数据
     *
     * 读取任务数据并把 cron 表达式解析回周期参数字段，供表单回填。
     * @param Request $request
     * @return Response
     */
    public function find(Request $request)
    {
        $id = (int) $request->get('id');
        $model = Crontab::find($id);
        if (!$model) {
            return $this->successRes([]);
        }
        $formData = $model->toArray();
        // 解析cron表达式为周期参数
        $cronParams = CronExpressionApi::make()->parseCronToParams($formData['cron_expression'] ?? '');
        $formData['cycle_type'] = $cronParams['cycle_type'];
        $formData['cycle_time'] = $cronParams['time'];
        // 根据周期类型设置对应的参数字段
        switch ($cronParams['cycle_type']) {
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
        return $this->successRes($formData);
    }

    /**
     * 删除
     * @param Request $request
     * @return Response
     */
    public function del(Request $request)
    {
        $id = (int) $request->get('id');
        CrontabApi::make()->del($id);
        return $this->success('删除成功');
    }

    /**
     * 导出定时任务
     * @param Request $request
     * @return Response
     */
    public function export(Request $request)
    {
        $id = (int) $request->get('id');
        CrontabApi::make()->exportCrontab([$id]);
        return $this->success('导出定时任务成功');
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
}