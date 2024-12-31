<?php
namespace app\backend\controller;

use support\Request;
use app\model\AdminLog;
use xbcode\XbController;
use xbcode\builder\ListBuilder;

/**
 * 系统日志
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class AdminLogController extends XbController
{
    /**
     * 模型
     * @var AdminLog
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
        parent::init();
        $this->model = new AdminLog;
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
        $builder->addColumn('id', '序号', [
            'width' => 80,
        ]);
        $builder->addColumn('admin', '管理员信息', [
            'minWidth' => 150,
            'params' => [
                'type' => 'imgtext',
                'props' => [
                    'title' => 'admin_name',
                    'desc' => 'admin_id',
                ],
            ],
        ]);
        $builder->addColumn('city', '城市信息', [
            'minWidth' => 180,
            'params' => [
                'type' => 'imgtext',
                'props' => [
                    'title' => 'city_name',
                    'desc' => 'real_ip',
                ],
            ],
        ]);
        $builder->addColumn('path', '请求地址', [
            'minWidth' => 180,
        ]);
        $builder->addColumn('type', '日志类型', [
            'width' => 100,
            'params' => [
                'type' => 'dict',
                'props' => [
                    'options' => [
                        '10' => '操作日志',
                        '20' => '登录日志',
                    ],
                    'style' => [
                        '10' => [
                            'type' => 'warning',
                        ],
                        '20' => [
                            'type' => 'info',
                        ],
                    ],
                ],
            ],
        ]);
        $builder->addColumn('method', '请求类型', [
            'width' => 80,
            'params' => [
                'type' => 'dict',
                'props' => [
                    'options' => [
                        'GET' => '无',
                        'POST' => '增加',
                        'PUT' => '修改',
                        'DELETE' => '删除',
                    ],
                    'style' => [
                        'GET' => [
                            'type' => 'info',
                        ],
                        'POST' => [
                            'type' => 'success',
                        ],
                        'PUT' => [
                            'type' => 'warning',
                        ],
                        'DELETE' => [
                            'type' => 'danger',
                        ],
                    ],
                ],
            ],
        ]);
        $builder->addColumn('query', '请求参数', [
            'minWidth' => 100,
        ]);
        $builder->addColumn('result', '响应参数', [
            'minWidth' => 100,
        ]);
        $builder->addColumn('create_at', '操作时间', [
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
        $model = $this->model;
        $data  = $model
            ->order('id desc')
            ->paginate()
            ->each(function ($item) {
                $item->admin_id   = "管理员ID:{$item->admin_id}";
                $item->admin_name = "账号:{$item->admin_name}";
            });
        return $this->successRes($data);
    }
}
