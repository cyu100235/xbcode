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
use plugin\xbAdmin\app\BaseController;
use plugin\xbAdmin\app\model\CrontabLog;

/**
 * 定时任务日志接口
 * @copyright 贵州云铺网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class CrontabLogController extends BaseController
{
    /**
     * 列表
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $crontabId = (int) $request->get('id');
        $where = [
            'crontab_id' => $crontabId,
        ];
        $model = CrontabLog::where($where)->with(['cron']);
        $data = $model->order('id desc')->paginate();
        return $this->successData($data);
    }

    /**
     * 清除日志
     * @param Request $request
     * @return Response
     */
    public function clear(Request $request)
    {
        try {
            $crontabId = (int) $request->get('crontab_id');
            $days = (int) $request->get('days');
            $model = CrontabLog::where('crontab_id', $crontabId);
            if ($days) {
                $model->where('create_at', '>=', date('Y-m-d', time() - $days * 86400));
            }
            $count = $model->count();
            $model->delete();
            return $this->success('成功清除 ' . $count . ' 条日志');
        } catch (\Throwable $e) {
            return $this->fail('清除日志失败：' . $e->getMessage());
        }
    }
}