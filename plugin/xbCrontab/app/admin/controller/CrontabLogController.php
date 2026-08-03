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

use plugin\xbCode\api\Url;
use plugin\xbCode\XbController;
use plugin\xbCode\builder\Renders\XbCrud;
use plugin\xbCrontab\app\model\CrontabLog;

/**
 * 定时任务日志控制器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class CrontabLogController extends XbController
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
            $crontabId = (int) request()->get('id');
            $where = [
                'crontab_id' => $crontabId,
            ];
            $model = CrontabLog::where($where)->with(['cron']);
            $data = $model->order('id desc')->paginate();
            return $this->successData($data);
        }
        $builder = XbCrud::make(function (XbCrud $builder) {
            $crontabId = (int) request()->get('id');
            // 顶部工具栏
            $builder->addHeaderConfirm('清除近7天日志', Url::make('clear')->query([
                'crontab_id' => $crontabId,
                'days' => 7,
            ]))->danger()->confirmLevel('确定要清除所有定时任务日志吗？此操作不可恢复！');
            $builder->addHeaderConfirm('清除近30天日志', Url::make('clear')->query([
                'crontab_id' => $crontabId,
                'days' => 30,
            ]))->danger()->confirmLevel('确定要清除所有定时任务日志吗？此操作不可恢复！');
            $builder->addHeaderConfirm('清除全部日志', Url::make('clear')->query([
                'crontab_id' => $crontabId,
            ]))->danger()->confirmLevel('确定要清除所有定时任务日志吗？此操作不可恢复！');

            $builder->addColumn('id', '序号')->width(100)->center();
            $builder->addColumn('cron.title', '所属任务');
            $builder->addColumn('run_second_time', '执行耗时(秒)')->width(150);
            $builder->addColumn('remarks', '执行备注');
            $builder->addColumn('create_at', '执行时间')->width(150);
        });
        return $this->successRes($builder);
    }

    /**
     * 清除日志
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function clear()
    {
        try {
            $crontabId = (int) request()->get('crontab_id');
            $days = (int) request()->get('days');
            $model = CrontabLog::where('crontab_id', $crontabId);
            $count = $model->count();
            if ($days) {
                $model->where('create_at', '>=', date('Y-m-d', time() - $days * 86400));
            }
            $model->delete();
            return $this->success('成功清除 ' . $count . ' 条日志');
        } catch (\Throwable $e) {
            return $this->fail('清除日志失败：' . $e->getMessage());
        }
    }
}