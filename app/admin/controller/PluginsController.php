<?php

namespace app\admin\controller;

use app\builder\ListBuilder;
use app\model\Plugins;
use app\service\cloud\PluginsCloud;
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
            'width' => 230
        ]);
        $builder->setTabs($data, 'active', $active);
        $builder->pageConfig();
        $builder->addRightButton('demo', '演示', [
            'type' => 'confirm',
            'api' => xbUrl('Plugins/demo'),
            'method' => 'delete',
        ], [
            'type' => 'success',
            'title' => '温馨提示',
            'content' => '是否确认安装该插件数据？',
        ], [
            'type' => 'primary',
            'icon' => 'UploadFilled',
        ]);
        $builder->addRightButton('order', '购买', [
            'type' => 'remote',
            'api' => xbUrl('Plugins/order'),
            'path' => '/vue'.xbUrl('plugins/order'),
            'method' => 'POST',
            'params' => [
                'field' => 'plugin_state',
                'value' => '10',
            ],
            'aliasParams' => [
                'name',
                'version'
            ],
        ], [
            'title' => '购买插件',
        ], [
            'type' => 'warning',
            'icon' => 'Download',
        ]);
        $builder->addRightButton('install', '安装', [
            'type' => 'confirm',
            'api' => xbUrl('Plugins/install'),
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
            'type' => 'success',
            'title' => '温馨提示',
            'content' => '是否确认安装该插件数据？',
        ], [
            'type' => 'success',
            'icon' => 'Download',
        ]);
        $builder->addRightButton('update', '更新', [
            'type' => 'confirm',
            'api' => xbUrl('Plugins/update'),
            'method' => 'PUT',
            'params' => [
                'field' => 'plugin_state',
                'value' => '40',
            ],
        ], [
            'type' => 'error',
            'title' => '温馨提示',
            'content' => '是否确认更新该插件？【谨慎】更新前请备份站点',
        ], [
            'type' => 'warning',
            'icon' => 'UploadFilled',
        ]);
        $builder->addRightButton('uninstall', '卸载', [
            'type' => 'confirm',
            'api' => xbUrl('Plugins/uninstall'),
            'method' => 'delete',
            'params' => [
                'field' => 'plugin_state',
                'where' => 'in',
                'value' => ['30', '40'],
            ],
        ], [
            'type' => 'error',
            'title' => '温馨提示',
            'content' => '是否确认卸载该插件？',
        ], [
            'type' => 'danger',
            'icon' => 'DeleteFilled',
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
        $builder->addScreen('keyword', 'input', '名称');
        $builder->addScreen('type', 'select', '类型', [
            'options' => $type,
        ]);
        $builder->addScreen('cid', 'select', '分类', [
            'options' => $type,
        ]);
        $builder->addColumn('author', '作者', [
            'width' => 150,
        ]);
        $builder->addColumn('desc', '介绍', [
        ]);
        $builder->addColumnEle('price_html', '价格', [
            'width' => 120,
            'params' => [
                'type' => 'html',
            ]
        ]);
        $builder->addColumn('version_name', '最新版本', [
            'width' => 100
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
        $active  = $request->get('active', 'plugins');
        $keyword = $request->get('keyword', '');
        $page    = (int)$request->get('page', 1);
        $limit   = (int)$request->get('limit', 20);
        if ($active == 'plugins')
        {
            $where = [];
            if ($keyword)
            {
                $where[] = ['title', 'like', "%{$keyword}%"];
            }
            $data = Plugins::where($where)
                ->order('id desc')
                ->paginate([
                    'page' => $page,
                    'list_rows' => $limit,
                ])
                ->toArray();
        }
        else
        {
            $data = PluginsCloud::pluginList($keyword, $page, $limit);
        }
        if (empty($data['data']))
        {
            return $this->successRes($data);
        }
        $list = $data['data'] ?? [];
        foreach ($list as &$value)
        {
            // 插件状态：10未购买，20未安装，30已安装，40有更新
            $value['plugin_state'] = '10';
            // 检测是否购买
            $isBuy = empty($value['is_buy']) ? '10' : $value['is_buy'];
            if ($isBuy === '20') {
                $value['plugin_state'] = '20';
            }
            // 检测是否安装
            $localVersion = Plugins::where('name', $value['name'])->value('version');
            if ($localVersion)
            {
                // 已安装
                $value['plugin_state'] = '30';
                // 检测是否可更新
                if ($value['version'] > $localVersion) {
                    $value['plugin_state'] = '40';
                }
            }
            // 插件价格
            $value['price_html'] = "<div style='color:#f56c6c;font-weight:700;'>免费</div>";
            if ($value['price'] > 0) {
                $money = "<div style='color:#f56c6c;font-weight:700;'>￥{$value['price']}</div>";
                $value['price_html'] = $money;
            }
            // 插件信息
            $value['plugin_title'] = "名称：{$value['title']}";
            $value['plugin_name']  = "标识：{$value['name']}";
        }
        $data['data'] = $list;
        return $this->successRes($data);
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
     * 安装插件
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function install(Request $request)
    {
        return CloudSerivce::install($request);
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
        return CloudSerivce::update($request);
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
        return CloudSerivce::uninstall($request);
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
        $name = $request->get('name', '');
        $version = $request->get('version', '');
        $data = CloudSerivce::pluginDetail($name, $version);
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
