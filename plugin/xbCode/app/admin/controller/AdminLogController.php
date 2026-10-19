<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\app\admin\controller;

use support\Request;
use support\Response;
use plugin\xbCode\api\Url;
use plugin\xbCode\enum\YesEnum;
use plugin\xbCode\app\model\Admin;
use plugin\xbCode\enum\LogActionEnum;
use plugin\xbCode\enum\LogResultEnum;
use plugin\xbCode\app\model\AdminLog;
use plugin\xbCode\app\model\AdminRole;
use plugin\xbCode\builder\Renders\XbForm;
use plugin\xbCode\builder\Renders\XbCrud;
use plugin\xbCode\exception\business\ExceptionForbidden;

/**
 * 日志管理
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class AdminLogController extends BaseController
{
    /**
     * 日志列表
     * @param Request $request
     * @return Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function index(Request $request)
    {
        if ($request->get('_act')) {
            $builder = XbCrud::make();
            // 清空日志属高危操作，仅系统管理员/系统角色可执行
            if ($this->isSystemAdmin()) {
                $builder->addHeaderConfirm('清空日志', Url::make('clear'), [
                    'title' => '清空日志',
                    'content' => '确定要清空所有操作日志吗？此操作不可恢复！',
                ])->danger();
            }
            $builder->addFilterInput('username', '管理员账号');
            $builder->addFilterInput('ip', '操作IP');
            $builder->addFilterSelect('action', '操作类型')->options(LogActionEnum::options())->clearable(true);
            $builder->addFilterSelect('result', '操作结果')->options(LogResultEnum::options())->clearable(true);
            $builder->addFilterDateRange('create_at', '操作时间');
            $builder->addColumn('id', '序号')->width(80)->align('center');
            $builder->addColumn('username', '管理员账号')->width(130);
            $builder->addColumn('menu_title', '操作菜单')->minWidth(140);
            $builder->addColumnMap('action', '操作类型', LogActionEnum::dict())->width(100);
            $builder->addColumn('content', '操作内容')->minWidth(260);
            $builder->addColumn('ip', '操作IP')->width(130);
            $builder->addColumn('city', '所在城市')->width(140);
            $builder->addColumnMap('result', '操作结果', LogResultEnum::dict())->width(100);
            $builder->addColumnDateTime('create_at', '操作时间')->width(160)->align('center');
            $builder->setActionConfig('width', 130);
            $builder->addRightActionDialog('查看', Url::make('show'), [
                'title' => '查看日志',
                'size' => 'lg',
                'actions' => [],
            ])->primary(true);
            $builder->addRightActionConfirm('删除', Url::make('del'));
            return $this->successRes($builder);
        }
        $query = AdminLog::order('id desc');
        if (($username = trim((string) $request->get('username', ''))) !== '') {
            $query->where('username', 'like', "%{$username}%");
        }
        if (($ip = trim((string) $request->get('ip', ''))) !== '') {
            $query->where('ip', 'like', "%{$ip}%");
        }
        if (in_array((string) $request->get('action', ''), LogActionEnum::getColumn('value'), true)) {
            $query->where('action', (string) $request->get('action'));
        }
        if (in_array((string) $request->get('result', ''), LogResultEnum::getColumn('value'), true)) {
            $query->where('result', (string) $request->get('result'));
        }
        $range = $request->get('create_at', '');
        if (is_string($range)) {
            $range = explode(',', $range);
        }
        $range = array_values(array_filter((array) $range, function ($item) {
            return trim((string) $item) !== '';
        }));
        if (count($range) === 2) {
            $query->whereBetween('create_at', [trim((string) $range[0]), trim((string) $range[1])]);
        }
        return $this->successData($query->paginate());
    }

    /**
     * 查看日志详情
     * @param Request $request
     * @return Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function show(Request $request)
    {
        $id = (int) $request->get('id');
        $model = AdminLog::where('id', $id)->find();
        if (!$model) {
            return $this->fail('数据不存在');
        }
        $data = $model->toArray();
        // 操作类型/结果转为可读文本
        $data['action_text'] = LogActionEnum::getFieldValue((string) $data['action'], (string) $data['action']);
        $data['result_text'] = LogResultEnum::getFieldValue((string) $data['result'], (string) $data['result']);
        // 操作参数格式化输出
        $data['params_text'] = $this->formatParams((string) $data['params']);
        $builder = XbForm::make();
        $builder->useForm()->wrapWithPanel(false);
        $builder->addRowGrid([
            $builder->addRowStatic('id', '序号'),
            $builder->addRowStatic('create_at', '操作时间'),
        ]);
        $builder->addRowGrid([
            $builder->addRowStatic('username', '管理员账号'),
            $builder->addRowStatic('menu_title', '操作菜单'),
        ]);
        $builder->addRowGrid([
            $builder->addRowStatic('action_text', '操作类型'),
            $builder->addRowStatic('result_text', '操作结果'),
        ]);
        $builder->addRowStatic('path', '请求地址');
        $builder->addRowTextarea('content', '操作内容')->disabled(true)->minRows(3);
        $builder->addRowCodeEdit('params_text', '操作参数')->language('json')->disabled(true)->size('lg');
        $builder->addRowGrid([
            $builder->addRowStatic('ip', '操作IP'),
            $builder->addRowStatic('city', '所在城市'),
        ]);
        $builder->addRowTextarea('user_agent', '浏览器标识')->disabled(true)->minRows(2);
        $builder->addRowTextarea('error', '失败原因')->disabled(true)->minRows(2);
        $builder->useForm()->static(true);
        // 只读查看，移除提交按钮
        $builder->addFormButton([]);
        $builder->setData($data);
        return $this->successRes($builder);
    }

    /**
     * 格式化操作参数
     * @param string $params 参数JSON字符串
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function formatParams(string $params): string
    {
        $params = trim($params);
        if ($params === '') {
            return '';
        }
        $data = json_decode($params, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $params;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    /**
     * 删除日志
     * @param Request $request
     * @return Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function del(Request $request)
    {
        $id = (int) $request->get('id');
        $model = AdminLog::where('id', $id)->find();
        if (!$model) {
            return $this->fail('数据不存在');
        }
        if (!$model->delete()) {
            return $this->fail('删除失败');
        }
        return $this->success('删除成功');
    }

    /**
     * 批量删除日志
     * @param Request $request
     * @return Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function delBatch(Request $request)
    {
        $ids = $request->get('id');
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }
        $ids = array_values(array_filter(array_map('intval', (array) $ids)));
        if (empty($ids)) {
            return $this->fail('请选择要删除的数据');
        }
        AdminLog::whereIn('id', $ids)->delete();
        return $this->success('删除成功');
    }

    /**
     * 清空日志
     * @param Request $request
     * @return Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function clear(Request $request)
    {
        // 清空日志属高危操作，仅系统管理员/系统角色可执行
        if (!$this->isSystemAdmin()) {
            throw new ExceptionForbidden('仅系统管理员可清空操作日志');
        }
        AdminLog::where('id', '>', 0)->delete();
        return $this->success('日志已清空');
    }

    /**
     * 是否系统管理员（管理员自身或所属角色 is_system=20）
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function isSystemAdmin(): bool
    {
        $adminId = (int) request()->uid;
        if ($adminId <= 0) {
            return false;
        }
        $isSystem = (string) Admin::where('id', $adminId)->value('is_system', '');
        if ($isSystem === YesEnum::YES['value']) {
            return true;
        }
        $roleId = (int) request()->role_id;
        if ($roleId <= 0) {
            return false;
        }
        $roleIsSystem = (string) AdminRole::where('id', $roleId)->value('is_system', '');
        return $roleIsSystem === YesEnum::YES['value'];
    }
}