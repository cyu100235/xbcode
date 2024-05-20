<?php

namespace app\admin\controller;

use app\builder\ListBuilder;
use app\service\action\PluginInstallAction;
use app\service\action\PluginUnInstallAction;
use app\service\action\PluginUpdateAction;
use app\service\CloudSerivce;
use app\XbController;
use support\Request;

/**
 * 插件管理
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginsController extends XbController
{
    /**
     * 表格渲染
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function indexTable(Request $request)
    {
        $active   = $request->get('active', 'plugins');
        $data     = [
            [
                'label' => '我的插件',
                'value' => 'plugins',
            ],
            [
                'label' => '插件市场',
                'value' => 'stores',
            ],
        ];
        $type     = [
            [
                'label' => '全部',
                'value' => 'all',
            ],
            [
                'label' => '免费',
                'value' => 'free',
            ],
            [
                'label' => '收费',
                'value' => 'charge',
            ],
        ];
        $formData = [
            'title' => '名称',
            'type' => '',
        ];
        $builder  = new ListBuilder;
        $builder->addActionOptions('操作', [
            'width' => 200
        ]);
        $builder->setTabs($data, 'active', $active);
        $builder->pageConfig();
        $builder->addTopButton('user', '云服务', [
            'type' => 'remote',
            'api' => xbUrl('Cloud/index'),
            'path' => '/vue' . xbUrl('cloud/index'),
            'method' => 'GET',
        ], [
            'title' => '云服务中心',
        ], [
            'type' => 'primary',
        ]);
        $builder->addRightButton('demo', '演示', [
            'type' => 'link',
            'api' => xbUrl('Plugins/demo'),
            'method' => 'GET',
            'params' => [
                'field' => 'demo_url',
                'where' => '!=',
                'value' => '',
            ],
        ], [
        ], [
            'type' => 'primary',
            'icon' => 'UploadFilled',
        ]);
        $builder->addRightButton('detail', '安装', [
            'type' => 'remote',
            'api' => xbUrl('Plugins/detail'),
            'path' => '/vue' . xbUrl('plugins/detail'),
            'method' => 'GET',
            'params' => [
                'field' => 'plugin_state',
                'value' => '10',
            ],
            'aliasParams' => [
                'name',
                'version'
            ],
        ], [
            'title' => '插件详情',
        ], [
            'type' => 'warning',
            'icon' => 'Download',
        ]);
        $builder->addRightButton('install', '安装', [
            'type' => 'confirm',
            'api' => xbUrl('Plugins/install'),
            'path' => '/vue' . xbUrl('Plugins/install'),
            'method' => 'POST',
            'params' => [
                'field' => 'plugin_state',
                'value' => '20',
            ],
            'aliasParams' => [
                'name',
                'version'
            ],
        ], [
            'type' => 'warning',
            'title' => '温馨提示',
            'content' => '是否确认开始安装该插件？',
            'loading' => '正在下载压缩包...',
        ], [
            'type' => 'primary',
            'icon' => 'Download',
        ]);
        $builder->addRightButton('config', '配置', [
            'type' => 'modal',
            'api' => xbUrl('Plugins/config'),
            'path' => '/vue' . xbUrl('Plugins/config'),
            'method' => 'GET',
            'params' => [
                'field' => 'plugin_config',
                'value' => '20',
            ],
            'aliasParams' => [
                'name',
                'version'
            ],
        ], [
            'title' => '插件配置',
        ], [
            'type' => 'info',
            'icon' => 'Setting',
        ]);
        $builder->addRightButton('update', '更新', [
            'type' => 'confirm',
            'api' => xbUrl('Plugins/update'),
            'path' => '/vue' . xbUrl('Plugins/update'),
            'method' => 'PUT',
            'params' => [
                'field' => 'plugin_state',
                'value' => '40',
            ],
            'aliasParams' => [
                'name',
                'version'
            ],
        ], [
            'type' => 'warning',
            'title' => '温馨提示',
            'content' => '是否确认开始更新该插件？',
            'loading' => '正在下载压缩包...',
        ], [
            'type' => 'warning',
            'icon' => 'UploadFilled',
        ]);
        $builder->addRightButton('uninstall', '卸载', [
            'type' => 'confirm',
            'api' => xbUrl('Plugins/uninstall'),
            'path' => '/vue' . xbUrl('plugins/uninstall'),
            'method' => 'DELETE',
            'params' => [
                'field' => 'plugin_state',
                'where' => 'in',
                'value' => ['30', '40'],
            ],
            'aliasParams' => [
                'name',
                'version'
            ],
        ], [
            'type' => 'error',
            'title' => '温馨提示',
            'content' => '是否确认卸载该插件？',
            'loading' => '正在准备卸载插件...',
        ], [
            'type' => 'danger',
            'icon' => 'DeleteFilled',
        ]);
        $builder->addScreen('keyword', 'input', '名称');
        $builder->addScreen('type', 'select', '类型', [
            'options' => $type,
        ]);
        $builder->addScreen('cid', 'select', '分类', [
            'options' => $type,
        ]);
        $builder->addColumnEle('plugins', '插件', [
            'width' => 300,
            'params' => [
                'type' => 'pictuer',
                'image' => 'logo',
                'title' => 'plugin_title',
                'desc' => 'plugin_name',
            ],
        ]);
        $builder->addColumn('author', '作者', [
            'width' => 120,
        ]);
        $builder->addColumn('cate_title', '分类', [
            'width' => 120,
        ]);
        $builder->addColumn('desc', '介绍', [
        ]);
        $builder->addColumnEle('price_html', '价格', [
            'width' => 120,
            'params' => [
                'type' => 'html',
            ]
        ]);
        $builder->addColumnEle('plugin_version', '版本', [
            'width' => 120,
            'params' => [
                'type' => 'html',
            ]
        ]);
        $builder->addColumn('down', '下载', [
            'width' => 100
        ]);
        $data = $builder->create();
        return $this->successRes($data);
    }

    /**
     * 列表
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        return CloudSerivce::pluginList($request);
    }

    /**
     * 安装插件
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function install(Request $request)
    {
        return PluginInstallAction::start($request);
    }

    /**
     * 更新插件
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function update(Request $request)
    {
        return PluginUpdateAction::start($request);
    }

    /**
     * 卸载插件
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function uninstall(Request $request)
    {
        return PluginUnInstallAction::start($request);
    }

    /**
     * 购买订单
     * @param \support\Request $request
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function order(Request $request)
    {
        return CloudSerivce::create($request);
    }

    /**
     * 统一下订单
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function unifiedOrder(Request $request)
    {
        return CloudSerivce::unifiedOrder($request);
    }

    /**
     * 插件详情
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function detail(Request $request)
    {
        $name    = $request->get('name', '');
        $version = $request->get('version', '');
        $data    = CloudSerivce::pluginDetail($name, $version);
        return $this->successRes($data);
    }

    /**
     * 插件配置
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function config(Request $request)
    {
        $name    = $request->get('name', '');
        $data    = CloudSerivce::pluginConfig($name);
        return $this->successRes($data);
    }

    /**
     * 插件演示
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function demo(Request $request)
    {
        $data = [];
        return $this->successRes($data);
    }
}
