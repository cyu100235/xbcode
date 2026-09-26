<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbAdmin\app\admin\controller;

use support\Request;
use support\Response;
use plugin\xbAdmin\api\Url;
use plugin\xbAdmin\enum\StateEnum;
use plugin\xbAdmin\enum\PeriodEnum;
use plugin\xbAdmin\enum\TaskTypeEnum;
use plugin\xbAdmin\enum\ExecuteModeEnum;
use plugin\xbAdmin\app\model\Crontab;
use plugin\xbCode\builder\Renders\XbForm;
use plugin\xbCode\builder\Renders\XbCrud;
use plugin\xbAdmin\app\validate\CrontabValidate;
use plugin\xbAdmin\exception\business\ExceptionBusiness;

/**
 * 定时任务管理
 *
 * 任务由 xbAdmin 的 crontab 进程（plugin/xbAdmin/app/process/Crontab.php）按 rule 调度执行，
 * 表单以「执行周期档位 + 各时间单位数值」配置，服务端合成周期性固定间隔配置入库。
 * 执行方式分两套：PHP 可调用（执行目标 + 执行参数）与命令执行（任务类型 + 任务命令）。
 * @copyright 贵州云铺网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class CrontabController extends BaseController
{
    /**
     * 定时任务列表
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        if ($request->get('_act')) {
            $builder = XbCrud::make();

            $builder->addHeaderDialog('添加定时任务', Url::make('add'), [
                'title' => '添加定时任务',
                'size' => 'lg',
            ]);

            // 筛选查询
            $builder->addFilterInput('keyword', '任务名称')
                ->clearable(true);
            $builder->addFilterSelect('state', '任务状态')
                ->options(StateEnum::options())->clearable(true);

            $builder->addColumn('id', '序号')->width(80)->align('center');
            $builder->addColumn('title', '任务名称')->width(180);
            $builder->addColumn('plugin', '所属插件')->width(120);
            $builder->addColumnMap('mode', '执行方式', ExecuteModeEnum::dict())->width(110);
            $builder->addColumnMap('type', '任务类型', TaskTypeEnum::dict())->width(130);
            $builder->addColumn('content_text', '执行内容');
            $builder->addColumn('rule_text', '执行周期')->width(200);
            $builder->addColumnMap('state', '任务状态', StateEnum::dict())->width(100);
            $builder->addColumn('last_time', '最后执行时间')->width(170);
            $builder->addColumn('run_count', '执行次数')->width(100);
            $builder->setActionConfig('width', 150);
            $builder->addRightActionDialog('修改', Url::make('edit'), [
                'title' => '修改定时任务',
                'size' => 'lg',
            ]);
            $builder->addRightActionConfirm('删除', Url::make('del'));
            return $this->successRes($builder);
        }
        $keyword = $request->get('keyword', '');
        $state = $request->get('state', '');
        $query = Crontab::order('id desc');
        if ($keyword) {
            $query->where('title', 'like', "%{$keyword}%");
        }
        if (in_array($state, array_column(StateEnum::toArray(), 'value'), true)) {
            $query->where('state', $state);
        }
        return $this->successData($query->paginate());
    }

    /**
     * 添加定时任务
     * @param Request $request
     * @return Response
     */
    public function add(Request $request)
    {
        if ($request->method() === 'POST') {
            $post = $this->prepareData((array) $request->post());
            xbValidate(CrontabValidate::class, $post, 'add');
            $model = new Crontab;
            if (!$model->save($post)) {
                throw new ExceptionBusiness('添加定时任务失败');
            }
            return $this->success('添加成功', ['id' => (int) $model['id']]);
        }
        $builder = $this->formView();
        $builder->setSaveMethod('POST');
        return $this->successRes($builder);
    }

    /**
     * 修改定时任务
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request)
    {
        $model = $this->findModel((int) $request->get('id'));
        if ($request->method() === 'PUT') {
            $post = $this->prepareData((array) $request->post());
            xbValidate(CrontabValidate::class, $post, 'edit');
            if (!$model->save($post)) {
                throw new ExceptionBusiness('修改失败');
            }
            return $this->success('修改成功');
        }
        $builder = $this->formView();
        $builder->setSaveMethod('PUT');
        // rule 为合成字段，编辑时反解成执行周期与各时间单位回填表单
        $data = array_merge($model->toArray(), Crontab::parseRule((string) $model['rule']));
        $builder->setData($data);
        return $this->successRes($builder);
    }

    /**
     * 删除定时任务
     * @param Request $request
     * @return Response
     */
    public function del(Request $request)
    {
        $model = $this->findModel((int) $request->get('id'));
        if (!$model->delete()) {
            throw new ExceptionBusiness('删除失败');
        }
        return $this->success('删除成功');
    }

    /**
     * 表单视图
     * @return XbForm
     */
    protected function formView(): XbForm
    {
        $builder = XbForm::make();

        // 基础信息
        $builder->addRowGroup('basic', [
            $builder->addRowInput('title', '任务名称')
                ->required(true)
                ->placeholder('请输入任务名称')
                ->description('示例：清除订单过期数据'),
            $builder->addRowSelect('plugin', '所属插件')
                ->options($this->pluginOptions())
                ->placeholder('请选择所属插件')
                ->description('任务归属的插件'),
            $builder->addRowInput('name', '任务标识')
                ->placeholder('请输入任务标识')
                ->description('该插件下的任务标识，如：clearExpireOrder'),
        ])->label('基础信息');

        // 执行配置
        $builder->addRowGroup('execute', [
            $builder->addRowSelect('mode', '执行方式')
                ->required(true)
                ->options(ExecuteModeEnum::options())
                ->value(ExecuteModeEnum::PHP['value'])
                ->description('PHP 可调用：填类名::方法名与 JSON 参数；命令执行：填 Shell 命令、URL 或 PHP 代码'),
            $builder->addRowSelect('type', '任务类型')
                ->options(TaskTypeEnum::options())
                ->value(TaskTypeEnum::SHELL['value'])
                ->visibleOn('this.mode == "command"')
                ->description('选择任务类型，决定任务命令的写法'),
            $builder->addRowInput('target', '执行目标')
                ->visibleOn('this.mode == "php"')
                ->placeholder('类名::方法名')
                ->description('PHP 可调用目标，格式：类名::方法名，如 app\\service\\Task::run'),
            $builder->addRowTextarea('params', '执行参数')
                ->rows(3)
                ->visibleOn('this.mode == "php"')
                ->placeholder('[1,"abc"]')
                ->description('JSON 数组，按顺序作为方法参数，示例：[1,"abc"]，无参数留空'),
            $builder->addRowTextarea('command', '任务命令')
                ->rows(3)
                ->visibleOn('this.mode == "command"')
                ->placeholder('请输入任务命令')
                ->description($this->commandDesc()),
        ])->label('执行配置');

        // 执行周期
        $builder->addRowGroup('cron', [
            $builder->addRowSelect('period', '周期类型')
                ->required(true)
                ->options(PeriodEnum::options())
                ->value(PeriodEnum::MINUTE['value'])
                ->description('按固定间隔执行，选中档位后逐级填写各单位数值，周按 7 天、月按 30 天计算'),
            $builder->addRowInputNumber('month', '月', 1)
                ->min(1)->max(999)
                ->placeholder('默认 1')
                ->visibleOn('this.period == "month"'),
            $builder->addRowInputNumber('week', '周', 1)
                ->min(1)->max(999)
                ->placeholder('默认 1')
                ->visibleOn('this.period == "week"'),
            $builder->addRowInputNumber('day', '日', 1)
                ->min(0)->max(30)
                ->placeholder('默认 1')
                ->visibleOn('this.period == "day" || this.period == "week" || this.period == "month"'),
            $builder->addRowInputNumber('hour', '时', 0)
                ->min(0)->max(23)
                ->placeholder('默认 0')
                ->visibleOn('this.period == "hour" || this.period == "day" || this.period == "week" || this.period == "month"'),
            $builder->addRowInputNumber('minute', '分', 1)
                ->min(0)->max(59)
                ->placeholder('默认 1')
                ->visibleOn('this.period != "second"'),
            $builder->addRowInputNumber('second', '秒', 0)
                ->min(0)->max(59)
                ->placeholder('默认 0')
                ->description('按固定间隔执行，如「每2小时30分」即每 2 小时 30 分执行一次'),
        ])->label('执行周期');

        // 任务状态
        $builder->addRowGroup('state', [
            $builder->addRowRadioButton('state', '任务状态', StateEnum::ENABLED['value'])
                ->required(true)
                ->options(StateEnum::options())
                ->description('禁用后不再调度执行'),
            $builder->addRowTextarea('remark', '备注')
                ->rows(2)
                ->placeholder('请输入备注'),
        ])->label('任务状态');

        return $builder;
    }

    /**
     * 所属插件选项
     *
     * 本地无插件选项接口，按 plugin.json 扫描现有插件，取插件目录名为标识、title 为显示名。
     * @return array
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function pluginOptions(): array
    {
        $options = [];
        foreach (glob(base_path('plugin/*/plugin.json')) ?: [] as $file) {
            $config = json_decode((string) file_get_contents($file), true);
            if (!is_array($config)) {
                continue;
            }
            $name = basename(dirname($file));
            $options[] = [
                'label' => (string) ($config['title'] ?? $name),
                'value' => $name,
            ];
        }
        return $options;
    }

    /**
     * 任务命令填写提示
     * @return string
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function commandDesc(): string
    {
        return <<<HTML
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
    }

    /**
     * 整理提交数据，合成周期配置
     * @param array $post
     * @throws ExceptionBusiness
     * @return array
     */
    protected function prepareData(array $post): array
    {
        foreach (['title', 'plugin', 'name', 'target', 'command', 'remark'] as $field) {
            $post[$field] = trim((string) ($post[$field] ?? ''));
        }
        // 下拉与按钮组可能提交数组，取首个元素
        foreach (['mode', 'type', 'period', 'state'] as $field) {
            if (isset($post[$field]) && is_array($post[$field])) {
                $post[$field] = current($post[$field]);
            }
        }
        if (!in_array($post['mode'] ?? '', array_column(ExecuteModeEnum::toArray(), 'value'), true)) {
            $post['mode'] = ExecuteModeEnum::PHP['value'];
        }
        if (!in_array($post['state'] ?? '', array_column(StateEnum::toArray(), 'value'), true)) {
            $post['state'] = StateEnum::ENABLED['value'];
        }
        if (!in_array($post['period'] ?? '', array_column(PeriodEnum::toArray(), 'value'), true)) {
            throw new ExceptionBusiness('执行周期错误');
        }
        // 两套执行字段按执行方式二选一，未选中的一侧归一化为空值
        if ($post['mode'] === ExecuteModeEnum::PHP['value']) {
            if (!preg_match('/^[\\\\\w]+::[\\\\\w]+$/', $post['target'])) {
                throw new ExceptionBusiness('执行目标格式错误，应为：类名::方法名');
            }
            $params = $post['params'] ?? '';
            if (is_string($params)) {
                $params = trim($params) === '' ? [] : json_decode($params, true);
            }
            // 只接受 JSON 数组，对象会解成关联数组，与「按顺序传参」的语义不符
            if (!is_array($params) || ($params !== [] && !array_is_list($params))) {
                throw new ExceptionBusiness('执行参数必须是合法的 JSON 数组');
            }
            $post['params'] = $params;
            $post['type'] = TaskTypeEnum::SHELL['value'];
            $post['command'] = '';
        } else {
            if (!in_array($post['type'] ?? '', array_column(TaskTypeEnum::toArray(), 'value'), true)) {
                throw new ExceptionBusiness('任务类型错误');
            }
            if ($post['command'] === '') {
                throw new ExceptionBusiness('请填写任务命令');
            }
            $post['target'] = '';
            $post['params'] = '';
        }
        // 合成周期性固定间隔配置
        $rule = Crontab::buildRule($post);
        if ($rule === '') {
            throw new ExceptionBusiness('执行周期错误，请检查执行周期与时间设置');
        }
        $post['rule'] = $rule;
        // 周期相关字段为表单虚拟字段，不入库
        unset($post['period'], $post['month'], $post['week'], $post['day'], $post['hour'], $post['minute'], $post['second']);
        return $post;
    }

    /**
     * 按ID查找定时任务
     * @param int $id
     * @throws ExceptionBusiness
     * @return Crontab
     */
    protected function findModel(int $id): Crontab
    {
        if ($id <= 0) {
            throw new ExceptionBusiness('数据不存在');
        }
        $model = Crontab::where('id', $id)->find();
        if (!$model) {
            throw new ExceptionBusiness('该数据不存在');
        }
        return $model;
    }
}