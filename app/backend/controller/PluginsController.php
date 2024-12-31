<?php
namespace app\backend\controller;

use support\Request;
use app\model\Plugins;
use xbcode\XbController;
use xbcode\builder\ListBuilder;
use xbcode\providers\DictProvider;
use xbcode\service\xbcode\OrdersPluginService;
use xbcode\service\xbcode\ProjectPluginService;
use xbcode\providers\plugins\PluginsUpdateProvider;
use xbcode\providers\plugins\PluginsInstallProvider;
use xbcode\providers\plugins\PluginsUninstallProvider;

/**
 * 插件控制器
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginsController extends XbController
{
    /**
     * 模型
     * @var Plugins
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
        $this->model = new Plugins;
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
        $builder = new ListBuilder;
        $builder->addActionOptions('操作', [
            'width' => 220
        ]);
        $builder->pageConfig();
        $builder->editConfig();
        $builder->rowConfig([
            'height' => 50
        ]);
        $options = [
            [
                'label' => '插件市场',
                'value' => 'market',
            ],
            [
                'label' => '我的插件',
                'value' => 'installed',
            ],
        ];
        $install = $request->get('install', 'market');
        $builder->setTabs($options, 'install', $install);
        $builder->addRightButton('install', '安装', [
            'type' => 'remote',
            'api' => xbUrl('Plugins/install'),
            'path' => xbUrl('Plugins/install'),
            'params' => [
                'field' => 'install',
                'value' => '20',
            ],
            'aliasParams' => [
                'name',
                'version_name',
                'version',
            ],
        ], [
            'title' => '插件安装',
            'customStyle' => [
                'width' => '650px',
                'height' => '65%',
            ],
        ], [
            'type' => 'primary',
            'icon' => 'Position',
        ]);
        $builder->addRightButton('update', '更新', [
            'type' => 'remote',
            'api' => xbUrl('Plugins/update'),
            'path' => xbUrl('Plugins/update'),
            'params' => [
                'field' => 'update',
                'value' => '20',
            ],
            'aliasParams' => [
                'name',
                'version_name',
                'version',
            ],
        ], [
            'title' => '插件更新',
            'customStyle' => [
                'width' => '650px',
                'height' => '65%',
            ],
        ], [
            'type' => 'success',
            'icon' => 'CloudDownloadOutlined',
        ]);
        $builder->addRightButton('enable', '启用', [
            'type' => 'confirm',
            'api' => xbUrl('Plugins/state'),
            'path' => xbUrl('Plugins/state'),
            'method' => 'PUT',
            'params' => [
                'field' => 'state',
                'value' => '10',
            ],
            'queryParams' => [
                'state' => '20',
            ],
        ], [
            'type' => 'warning',
            'title' => '温馨提示',
            'content' => '是否确定启用插件？',
        ], [
            'type' => 'success',
            'icon' => 'IssuesCloseOutlined',
        ]);
        $builder->addRightButton('disable', '禁用', [
            'type' => 'confirm',
            'api' => xbUrl('Plugins/state'),
            'path' => xbUrl('Plugins/state'),
            'method' => 'PUT',
            'params' => [
                'field' => 'state',
                'value' => '20',
            ],
            'queryParams' => [
                'state' => '10',
            ],
        ], [
            'type' => 'warning',
            'title' => '温馨提示',
            'content' => '是否确定禁用插件？',
        ], [
            'type' => 'warning',
            'icon' => 'InfoCircleOutlined',
        ]);
        $builder->addRightButton('uninstall', '卸载', [
            'type' => 'remote',
            'api' => xbUrl('Plugins/uninstall'),
            'path' => xbUrl('Plugins/uninstall'),
            'params' => [
                'field' => 'uninstall',
                'value' => '20',
            ],
            'aliasParams' => [
                'name',
                'version_name',
                'version',
            ],
        ], [
            'title' => '卸载插件',
            'customStyle' => [
                'width' => '650px',
                'height' => '65%',
            ],
        ], [
            'type' => 'danger',
            'icon' => 'ClearOutlined',
        ]);
        $builder->addRightButton('buy', '购买', [
            'type' => 'remote',
            'api' => xbUrl('Plugins/buy'),
            'path' => xbUrl('Plugins/buy'),
            'params' => [
                'field' => 'is_buy',
                'value' => '10',
            ],
            'aliasParams' => [
                'name',
                'version_name',
                'version',
            ],
        ], [
            'title' => '插件购买',
        ], [
            'type' => 'success',
            'icon' => 'GiftOutlined',
        ]);
        $builder->addScreen('keyword', '$input', '关键词', '', [
            'placeholder' => '请输入关键词',
        ]);
        $pluginTypes = [
            [
                'label' => '内置插件',
                'value' => 10,
            ],
            [
                'label' => '扩展插件',
                'value' => 20,
            ],
        ];
        $pluginType  = (int) $request->get('type', '');
        $builder->addScreen('type', '$select', '插件类型', $pluginType, [
            'placeholder' => '请选择插件类型',
            'options' => array_merge([
                [
                    'label' => '全部插件',
                    'value' => 0,
                ]
            ], $pluginTypes),
        ]);
        $builder->addColumn('plugin', '插件信息', [
            'width' => 200,
            'params' => [
                'type' => 'imgtext',
                'props' => [
                    'image' => 'plugin_logo',
                    'title' => 'title',
                    'desc' => 'name',
                ],
            ],
        ]);
        $builder->addColumn('desc', '介绍描述', [
        ]);
        $pluginTypeDict = array_column($pluginTypes, 'label', 'value');
        $builder->addColumn('type', '插件类型', [
            'width' => 100,
            'params' => [
                'type' => 'dict',
                'props' => [
                    'options' => $pluginTypeDict,
                    'style' => [
                        10 => [
                            'type' => 'warning',
                        ],
                        20 => [
                            'type' => 'success',
                        ],
                    ],
                ],
            ],
        ]);
        $builder->addColumn('version_name', '最新版本', [
            'width' => 100,
        ]);
        $stateDict = DictProvider::get('stateText')->dict();
        $stateDict['30'] = '未安装';
        $builder->addColumn('state', '插件状态', [
            'width' => 100,
            'params' => [
                'type' => 'dict',
                'props' => [
                    'options' => $stateDict,
                    'style' => [
                        10 => [
                            'type' => 'warning',
                        ],
                        20 => [
                            'type' => 'success',
                        ],
                    ],
                ],
            ],
        ]);
        if ($install === 'installed') {
            $builder->addColumn('local_version_name', '本地版本', [
                'width' => 100,
            ]);
        }
        $builder->addColumn('price', '插件售价', [
            'width' => 100,
            'style' => [
                'color' => 'red',
            ],
            'params' => [
                'style' => [
                    'color' => 'red',
                ],
            ],
        ]);
        $data = $builder->create();
        return $this->successRes($data);
    }

    /**
     * 插件视图
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        $install = $request->get('install', 'market');
        $page    = (int) $request->get('page', 1);
        $limit   = (int) $request->get('limit', 30);
        $model   = $this->model;
        // 获取已购买插件标识名
        $plugins = $model->pluginDict();
        // 是否获取已安装插件
        $installed = $install === 'installed' ? true : false;
        // 获取插件列表
        $data = ProjectPluginService::datalist($plugins, $installed, $page, $limit);
        // 重设数据
        $data['data'] = array_map(function ($item) use ($plugins) {
            // 本地插件ID
            $item['id'] = $plugins[$item['name']]['id'] ?? null;
            // 本地插件版本
            $item['local_version_name'] = $plugins[$item['name']]['version_name'] ?? '';
            // 插件状态
            $item['state'] = $plugins[$item['name']]['state'] ?? '30';
            // 返回数据
            return $item;
        }, $data['data']);
        // 返回数据
        return $this->successRes($data);
    }

    /**
     * 安装插件
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function install(Request $request)
    {
        if ($request->method() === 'POST') {
            $step        = (string) $request->get('step', '');
            $name        = (string) $request->post('name', '');
            $versionName = (string) $request->post('version_name', '');
            $version     = (int) $request->post('version');
            if (empty($step)) {
                return $this->fail('安装步骤参数错误');
            }
            if (empty($name)) {
                return $this->fail('插件标识参数错误');
            }
            if (empty($versionName)) {
                return $this->fail('插件版本参数错误');
            }
            if (empty($version)) {
                return $this->fail('插件版本编号参数错误');
            }
            // 安装插件
            $service = new PluginsInstallProvider;
            // 执行安装
            return $service->start($step, $name, $versionName, $version);
        }
        return $this->view('view/backend/plugins/install');
    }

    /**
     * 更新插件
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function update(Request $request)
    {
        if ($request->method() === 'PUT') {
            $step        = (string) $request->get('step', '');
            $name        = (string) $request->post('name', '');
            $versionName = (string) $request->post('version_name', '');
            $version     = (int) $request->post('version');
            if (empty($step)) {
                return $this->fail('安装步骤参数错误');
            }
            if (empty($name)) {
                return $this->fail('插件标识参数错误');
            }
            if (empty($versionName)) {
                return $this->fail('插件版本参数错误');
            }
            if (empty($version)) {
                return $this->fail('插件版本编号参数错误');
            }
            // 实例服务
            $service = new PluginsUpdateProvider;
            // 执行安装
            return $service->start($step, $name, $versionName, $version);
        }
        return $this->view('view/backend/plugins/update');
    }

    /**
     * 卸载插件
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function uninstall(Request $request)
    {
        if ($request->method() === 'DELETE') {
            $step        = (string) $request->get('step', '');
            $name        = (string) $request->post('name', '');
            $versionName = (string) $request->post('version_name', '');
            $version     = (int) $request->post('version');
            if (empty($step)) {
                return $this->fail('安装步骤参数错误');
            }
            if (empty($name)) {
                return $this->fail('插件标识参数错误');
            }
            if (empty($versionName)) {
                return $this->fail('插件版本参数错误');
            }
            if (empty($version)) {
                return $this->fail('插件版本编号参数错误');
            }
            // 实例服务
            $service = new PluginsUninstallProvider;
            // 执行安装
            return $service->start($step, $name, $versionName, $version);
        }
        return $this->view('view/backend/plugins/uninstall');
    }

    /**
     * 设置插件状态
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function state(Request $request)
    {
        $id    = (int) $request->post('id');
        $state = (string) $request->post('state');
        if (empty($id)) {
            return $this->fail('插件参数错误');
        }
        $model = $this->model;
        $model = $model->where('id', $id)->find();
        if (empty($model)) {
            return $this->fail('插件不存在');
        }
        if (!$model->save(['state' => $state])) {
            return $this->fail('插件状态设置失败');
        }
        // 刷新缓存
        $cacheModel = $this->model;
        $cacheModel->getAuthData($model['name'], $model['version_name'], $model['version'], true);
        // 返回数据
        $message = $state === '20' ? '插件启用成功' : '插件禁用成功';
        return $this->success($message);
    }

    /**
     * 购买插件
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function buy(Request $request)
    {
        if ($request->isPost()) {
            $name = $request->post('name');
            if (empty($name)) {
                return $this->fail('插件标识参数错误');
            }
            // 创建订单
            $orderNo = OrdersPluginService::create($name);
            // 统一下单
            $data = OrdersPluginService::unifiedOrder($orderNo);
            // 返回数据
            return $this->response($data);
        }
        return $this->view('view/backend/plugins/buy');
    }

    /**
     * 轮询检查插件是否付款
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function checked(Request $request)
    {
        $orderNo = (string) $request->get('order_no');
        if (empty($orderNo)) {
            return $this->fail('订单号参数错误');
        }
        // 轮询检查插件是否付款
        $result = OrdersPluginService::checked($orderNo);
        // 返回数据
        return $this->successFul('插件购买完成', [
            'checked' => $result
        ]);
    }

    /**
     * 获取插件详情
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function detail(Request $request)
    {
        $name        = (string) $request->get('name');
        $versionName = (string) $request->get('version_name');
        if (empty($name)) {
            return $this->fail('插件标识参数错误');
        }
        if (empty($versionName)) {
            return $this->fail('插件版本参数错误');
        }
        // 获取项目插件详情
        $data = ProjectPluginService::projectPluginVersion($name, $versionName);
        // 返回数据
        return $this->successRes($data);
    }
}
