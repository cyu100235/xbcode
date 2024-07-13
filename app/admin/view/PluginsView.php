<?
namespace app\admin\view;

use app\common\builder\ListBuilder;

/**
 * 插件视图
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginsView
{
    /**
     * 本地插件视图
     * @param \app\common\builder\ListBuilder $builder
     * @return ListBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function plugins(ListBuilder $builder)
    {
        // 插件状态：10 待下载，20待安装，30已安装，40有更新
        $builder->addTopButton('import', '导入插件', [
            'type' => 'upload',
            'api' => xbUrl('PluginsAction/import'),
            'path' => xbUrl('PluginsAction/import'),
        ], [], [
            'type' => 'success',
        ]);
        $builder->addRightButton('install', '安装', [
            'type' => 'confirm',
            'api' => xbUrl('PluginsAction/install'),
            'path' => '/vue' . xbUrl('PluginsAction/install'),
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
        $builder->addRightButton('update', '更新', [
            'type' => 'confirm',
            'api' => xbUrl('PluginsAction/update'),
            'path' => '/vue' . xbUrl('PluginsAction/update'),
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
        $builder->addRightButton('config', '配置', [
            'type' => 'modal',
            'api' => xbUrl('PluginsAction/config'),
            'path' => '/vue' . xbUrl('PluginsAction/config'),
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
        $builder->addRightButton('export', '导出', [
            'type' => 'confirm',
            'api' => xbUrl('PluginsAction/export'),
            'path' => xbUrl('PluginsAction/export'),
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
            'title' => '温馨提示',
            'content' => '导出插件前，请自行备份数据库至插件目录',
            'loading' => '正在导出插件...',
        ], [
            'type' => 'primary',
            'icon' => 'Position',
        ]);
        $builder->addRightButton('uninstall', '卸载', [
            'type' => 'confirm',
            'api' => xbUrl('PluginsAction/uninstall'),
            'path' => '/vue' . xbUrl('PluginsAction/uninstall'),
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
        return $builder;
    }

    /**
     * 商店视图
     * @param \app\common\builder\ListBuilder $builder
     * @return ListBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function store(ListBuilder $builder)
    {
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
            'api' => xbUrl('PluginsAction/demo'),
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
        $builder->addRightButton('detail', '购买', [
            'type' => 'remote',
            'api' => xbUrl('PluginsAction/detail'),
            'path' => '/vue' . xbUrl('PluginsAction/detail'),
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
        $type = [
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
        $builder->addScreen('type', 'select', '类型', [
            'options' => $type,
        ]);
        $builder->addScreen('cid', 'select', '分类', [
            'options' => $type,
        ]);
        $builder->addColumnEle('price_html', '价格', [
            'width' => 120,
            'params' => [
                'type' => 'html',
            ]
        ]);
        $builder->addColumn('down', '下载', [
            'width' => 100
        ]);
        return $builder;
    }
}