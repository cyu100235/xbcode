<?php
/**
 * 贵州猿创科技有限公司
 *
 * @package  XhAdmin
 * @author   楚羽幽 <958416459@qq.com>
 * @version  1.0
 * @license  Apache License 2.0
 * @link     http://www.xhadmin.cn
 * @document http://doc.xhadmin.cn
 */
namespace plugin\xbCrontab\app\admin\controller;

use plugin\xbCode\enum\StateEnum;
use support\Request;
use plugin\xbCode\XbController;
use plugin\xbCode\api\PluginsApi;
use plugin\xbCode\builder\Builder;
use plugin\xbCrontab\api\CrontabApi;
use plugin\xbCrontab\app\model\Crontab;
use plugin\xbCode\builder\Renders\Form;
use plugin\xbCrontab\enum\TaskTypeEnum;
use plugin\xbCrontab\enum\TaskCycleEnum;
use plugin\xbCode\builder\Renders\TableCrud;

/**
 * 首页控制器
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
            $keyword = request()->get('keyword');
            $where = [];
            if ($keyword) {
                $where[] = ['title', 'like', "%{$keyword}%"];
            }
            $data = Crontab::where($where)
                ->paginate()
                ->each(function ($item) {
                    $unit = TaskCycleEnum::getFieldValue($item->unit, null, 'unit');
                    $item->exce_cycle = "每{$unit}执行 {$item->exec_date} 次";
                    $item->run_second_time = "{$item->run_second_time} 秒";
                });
            return $this->successData($data);
        }
        $builder = Builder::crud(function (TableCrud $builder) {
            // 头部
            $builder->addHeaderDialog('添加', xbUrl('add'), [
                'title' => '添加定时任务',
            ]);
            // 表列
            $builder->addColumn('title', '任务名称');
            $builder->addColumn('plugin', '所属插件');
            $builder->addColumnMap('type', '任务类型', TaskTypeEnum::dict())->width(130);
            $builder->addColumn('exce_cycle', '执行周期');
            $builder->addColumnMap('state', '任务状态', StateEnum::dict())->width(100);
            $builder->addColumn('run_second_time', '执行时长')->width(100);
            $builder->addColumn('last_time', '最后执行')->width(150);
            $builder->addColumn('error', '失败原因')->width(150);
            $builder->addColumn('update_at', '更新时间')->width(150);
            $builder->addColumn('create_at', '创建时间')->width(150);
            // 操作
            $builder->setActionConfig('width', 180);
            $builder->addRightActionConfirm('执行', xbUrl('working'), '是否确认执行该定时任务？')
            ->className('text-info');
            $builder->addRightActionConfirm('导出', xbUrl('export'), '是否确认导出该定时任务？')
                ->className('text-success');
            $builder->addRightActionDialog('设置', xbUrl('edit'));
            $builder->addRightActionConfirm('删除', xbUrl('del'), '是否确认删除该定时任务？')
                ->className('text-danger');
        });
        return $this->successRes($builder);
    }

    /**
     * 添加
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function add()
    {
        if (request()->method() === 'POST') {
            $post = request()->post();
            CrontabApi::make()->add($post);
            return $this->success('添加成功');
        }
        $builder = $this->formView();
        $builder->setMethod('POST');
        return $this->successRes($builder);
    }

    /**
     * 编辑
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function edit()
    {
        if (request()->method() === 'PUT') {
            $post = request()->post();
            CrontabApi::make()->add($post);
            return $this->success('添加成功');
        }
        $builder = $this->formView();
        $builder->setMethod('PUT');
        return $this->successRes($builder);
    }

    /**
     * 删除
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function del()
    {
        $id = (int) request()->get('id');
        CrontabApi::make()->del($id);
        return $this->success('删除成功');
    }

    /**
     * 表单视图
     * @return Form
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function formView()
    {
        return Builder::form(function (Form $builder) {
            $plugins = PluginsApi::make()->list('20');
            $plugins = array_map(function ($item) {
                return [
                    'label' => "{$item['title']}（{$item['name']}）",
                    'value' => $item['name'],
                ];
            }, $plugins);
            $builder->addRowGroup([
                $builder->addRowInput('title', '任务名称')
                    ->desc('示例：清除订单过期数据'),
                $builder->addRowSelect('plugin', '所属插件')
                    ->desc('选择执行的插件任务')
                    ->options($plugins),
            ]);
            $builder->addRowGroup([
                $builder->addRowSelect('type', '任务类型')
                    ->desc('选择执行的任务类型')
                    ->options(TaskTypeEnum::options()),
                $builder->addRowInputGroup('unit', '执行周期')
                    ->desc('选择执行的插件任务周期时间')
                    ->body([
                        [
                            'type' => 'select',
                            'name' => 'unit',
                            'label' => '执行周期',
                            'value' => '10',
                            'options' => TaskCycleEnum::options(),
                        ],
                        [
                            'type' => 'input-text',
                            'name' => 'exec_date',
                            'label' => '执行时间',
                        ],
                        [
                            'type' => 'button',
                            'label' => '执行1次',
                            'level' => 'secondary',
                        ],
                    ]),
            ]);
            $builder->addRowTextarea('command', '任务命令')
                ->desc('根据任务类型编写执行命令，示例：php xb.php plugin:xbOrder:clearExpire');
        });
    }
}