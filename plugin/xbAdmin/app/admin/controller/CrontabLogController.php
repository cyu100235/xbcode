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
use plugin\xbCode\api\Gateway;
use plugin\xbAdmin\app\BaseController;
use plugin\xbCode\builder\Renders\XbCrud;

/**
 * 定时任务日志控制器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class CrontabLogController extends BaseController
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
            $result = Gateway::get('xbAdmin/api/CrontabLog/index', $request->get());
            return $this->response($result);
        }
        $builder = XbCrud::make();
        $crontabId = (int) $request->get('id');
        // 顶部工具栏
        $builder->addHeaderConfirm('清除近7天日志', Url::make('clear')->query([
            'crontab_id' => $crontabId,
            'days' => 7,
        ]), [
            'title' => '清除日志',
            'content' => '确定要清除近7天的定时任务日志吗？此操作不可恢复！',
        ])->danger();
        $builder->addHeaderConfirm('清除近30天日志', Url::make('clear')->query([
            'crontab_id' => $crontabId,
            'days' => 30,
        ]), [
            'title' => '清除日志',
            'content' => '确定要清除近30天的定时任务日志吗？此操作不可恢复！',
        ])->danger();
        $builder->addHeaderConfirm('清除全部日志', Url::make('clear')->query([
            'crontab_id' => $crontabId,
        ]), [
            'title' => '清除日志',
            'content' => '确定要清除所有定时任务日志吗？此操作不可恢复！',
        ])->danger();

        $builder->addColumn('id', '序号')->width(100)->center();
        $builder->addColumn('cron.title', '所属任务');
        $builder->addColumn('run_second_time', '执行耗时(秒)')->width(150);
        $builder->addColumn('remarks', '执行备注');
        $builder->addColumn('create_at', '执行时间')->width(150);
        return $this->successRes($builder);
    }

    /**
     * 清除日志
     * @param Request $request
     * @return Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function clear(Request $request)
    {
        $result = Gateway::get('xbAdmin/api/CrontabLog/clear', $request->get());
        return $this->response($result);
    }
}