<?php
namespace app\backend\controller;

use support\Request;
use app\model\Crontab;
use xbcode\XbController;
use xbcode\builder\FormBuilder;
use xbcode\builder\ListBuilder;
use app\validate\CrontabValidate;

/**
 * 定时任务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class CrontabController extends XbController
{
    /**
     * 模型
     * @var Crontab
     */
    protected $model;

    /**
     * 初始化
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function init()
    {
        $this->model = new Crontab;
    }

    /**
     * 表格
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function indexTable(Request $request)
    {
        // 表格渲染
        $builder = new ListBuilder;
        $builder->pageConfig();
        $builder->rowConfig([
            'height' => 45
        ]);
        $description = <<<STR
        <div>定时任务规则描述</div>
        <div>0   1   2   3   4   5</div>
        <div style="padding-top:15px;"><span style="margin-right:50px">参数1: 每周 (0 - 6) (周日=0)</span>参数2: 每月 (1 - 12)</div>
        <div><span style="margin-right:50px">参数3: 年月 (1 - 31)</span>参数4: 小时 (0 - 23)</div>
        <div><span style="margin-right:50px">参数5: 分账 (0 - 59)</span>参数6: 秒级 (0-59)[可省略，如果没有0位,则最小时间粒度是分钟]</div>
        <div style="padding-top:15px;">注意：定时任务不会马上执行，所有定时任务进入下一分钟才会开始计时执行</div>
        STR;
        $builder->addDesc($description, [
            'type' => 'warning',
            'showIcon' => true,
            'closable' => false,
        ], [
            'color' => '#f56c6c'
        ]);
        $builder->addActionOptions('操作', [
            'width' => 230
        ]);
        $builder->addTopButton('add', '添加', [
            'type' => 'modal',
            'api' => xbUrl('Crontab/add'),
            'path' => xbUrl('Crontab/add'),
        ], [
            'title' => '添加定时任务',
            'customStyle' => [
                'width' => '500px'
            ],
        ], [
            'type' => 'primary',
            'icon' => 'Plus'
        ]);
        $builder->addRightButton('start', '启动', [
            'type' => 'confirm',
            'api' => xbUrl('Crontab/action'),
            'path' => xbUrl('Crontab/action'),
            'method' => 'PUT',
            'params' => [
                'field' => 'state',
                'where' => 'in',
                'value' => ['10', '30']
            ],
            'queryParams' => [
                'state' => '20'
            ],
        ], [
            'type' => 'success',
            'title' => '温馨提示',
            'content' => '是否确认启动该任务？',
        ], [
            'type' => 'success',
            'icon' => 'EditPen'
        ]);
        $builder->addRightButton('stop', '停止', [
            'type' => 'confirm',
            'api' => xbUrl('Crontab/action'),
            'path' => xbUrl('Crontab/action'),
            'method' => 'PUT',
            'params' => [
                'field' => 'state',
                'value' => '20'
            ],
            'queryParams' => [
                'state' => '10'
            ],
        ], [
            'type' => 'warning',
            'title' => '温馨提示',
            'content' => '是否确认停止该任务？',
        ], [
            'type' => 'warning',
            'icon' => 'VideoPause'
        ]);
        $builder->addRightButton('edit', '修改', [
            'type' => 'modal',
            'api' => xbUrl('Crontab/edit'),
            'path' => xbUrl('Crontab/edit'),
        ], [
            'title' => '修改定时任务',
            'customStyle' => [
                'width' => '500px'
            ],
        ], [
            'type' => 'primary',
            'icon' => 'EditOutlined'
        ]);
        $builder->addRightButton('del', '删除', [
            'type' => 'confirm',
            'api' => xbUrl('Crontab/del'),
            'path' => xbUrl('Crontab/del'),
            'method' => 'DeleteOutlined',
            'params' => [
                'field' => 'is_system',
                'value' => '10'
            ],
        ], [
            'type' => 'error',
            'title' => '温馨提示',
            'content' => '是否确认删除该数据？',
        ], [
            'type' => 'danger',
            'icon' => 'DeleteOutlined'
        ]);
        $builder->addColumn('title', '任务名称', [
            'minWidth' => 180,
        ]);
        $builder->addColumn('expression', '任务规则', [
            'minWidth' => 180,
        ]);
        $builder->addColumn('command', '执行命令', [
            'minWidth' => 180,
        ]);
        $builder->addColumn('state', '运行状态', [
            'width' => 100,
            'params' => [
                'type' => 'dict',
                'props' => [
                    'options' => [
                        '10' => '已停止',
                        '20' => '运行中',
                        '30' => '已错误',
                    ],
                    'style' => [
                        '10' => [
                            'type' => 'danger'
                        ],
                        '20' => [
                            'type' => 'success'
                        ],
                        '30' => [
                            'type' => 'danger'
                        ],
                    ]
                ]
            ]
        ]);
        $builder->addColumn('run_time', '实时执行', [
            'width' => 100,
        ]);
        $builder->addColumn('run_time', '最大执行', [
            'width' => 100,
        ]);
        $builder->addColumn('last_time', '最后执行', [
            'width' => 160,
        ]);
        $builder->addColumn('error', '失败原因', [
            'minWidth' => 180,
        ]);
        $builder->addColumn('create_at', '创建时间', [
            'width' => 160,
        ]);
        $data = $builder->create();
        return $this->successRes($data);
    }

    /**
     * 列表
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        $data = Crontab::order('id asc')->paginate()->each(function ($item) {
            $item->error = $item->error ?: '--';
        });
        return $this->successRes($data);
    }

    /**
     * 操作任务
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function action(Request $request)
    {
        $id    = $request->post('id');
        $state = $request->post('state');
        $model = Crontab::where('id', $id)->find();
        if (!$model) {
            return $this->fail('该数据不存在');
        }
        if (empty($state)) {
            return $this->fail('操作状态错误');
        }
        $data = [
            'state' => $state,
            'error' => '',
            'run_time' => '',
            'last_time' => null,
            'max_time' => '',
        ];
        if (!$model->save($data)) {
            return $this->fail('操作失败');
        }
        // 更新缓存
        Crontab::getCrontabCache(true);
        // 返回结果
        return $this->success('操作成功');
    }

    /**
     * 添加
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function add(Request $request)
    {
        if ($request->method() == 'POST') {
            $post = $request->post();

            // 数据验证
            xbValidate(CrontabValidate::class, $post, 'add');

            $model = new Crontab;
            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            // 更新缓存
            Crontab::getCrontabCache(true);
            // 返回结果
            return $this->success('保存成功');
        }
        $builder = $this->formView();
        $builder->setMethod('POST');
        $data = $builder->create();
        return $this->successRes($data);
    }

    /**
     * 修改
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function edit(Request $request)
    {
        $id    = $request->get('id');
        $model = Crontab::where('id', $id)->find();
        if (!$model) {
            return $this->fail('该数据不存在');
        }
        if ($request->method() == 'PUT') {
            $post = $request->post();

            // 数据验证
            xbValidate(CrontabValidate::class, $post, 'edit');

            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            // 更新缓存
            Crontab::getCrontabCache(true);
            // 返回结果
            return $this->success('保存成功');
        }
        $builder = $this->formView();
        $builder->setMethod('PUT');
        $builder->setData($model);
        $data = $builder->create();
        return $this->successRes($data);
    }

    /**
     * 删除
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function del(Request $request)
    {
        $id    = $request->post('id');
        $model = Crontab::where('id', $id)->find();
        if (!$model) {
            return $this->fail('该数据不存在');
        }
        if (!$model->delete()) {
            return $this->fail('删除失败');
        }
        // 更新缓存
        Crontab::getCrontabCache(true);
        // 返回结果
        return $this->success('删除成功');
    }

    /**
     * 表单
     * @return FormBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function formView()
    {
        $builder = new FormBuilder;
        $builder->addRow('title', 'input', '任务名称', '', [
            'prompt' => '请输入任务名称'
        ]);
        $builder->addRow('command', 'input', '执行命令', '', [
            'prompt' => '请输入exec命令，如：ls'
        ]);
        $builder->addRow('params', 'input', '任务参数', '', [
            'prompt' => '请输入命令参数，如：--name 任务参数'
        ]);
        $builder->addRow('expression', 'input', '任务规则', '', [
            'prompt' => '请输入crontab规则，如：* * * * * *'
        ]);
        return $builder;
    }
}
