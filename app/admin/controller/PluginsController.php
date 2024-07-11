<?php

namespace app\admin\controller;

use app\admin\view\PluginsView;
use app\common\builder\ListBuilder;
use app\common\service\CloudSerivce;
use app\common\XbController;
use app\model\Plugins;
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
        $active = $request->get('active', 'plugins');
        $data   = [
            [
                'label' => '我的插件',
                'value' => 'plugins',
            ],
        ];
        if (xbEnv('PLUGIN_STORE', false)) {
            $data[] = [
                'label' => '插件市场',
                'value' => 'store'
            ];
        }
        $builder = new ListBuilder;
        $builder->addActionOptions('操作', [
            'width' => 200
        ]);
        $builder->setTabs($data, 'active', $active);
        $builder->pageConfig();
        // 筛选查询
        $builder->addScreen('keyword', 'input', '名称');
        // 表格列
        $builder->addColumnEle('plugins', '插件', [
            'minWidth' => 200,
            'params' => [
                'type' => 'pictuer',
                'image' => 'logo',
                'title' => 'title',
                'desc' => 'plugin_name',
            ],
        ]);
        $builder->addColumn('version', '版本', [
            'width' => 120,
        ]);
        $builder->addColumn('author', '作者', [
            'minWidth' => 100,
        ]);
        $builder->addColumn('desc', '介绍');
        $class   = new PluginsView;
        $builder = call_user_func([$class, $active], $builder);
        $data    = $builder->create();
        return $this->successRes($data);
    }

    /**
     * 插件转发
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        $active = $request->get('active', 'plugins');
        if (!method_exists($this, $active)) {
            return $this->fail('方法不存在');
        }
        return call_user_func([$this, $active], $request);
    }

    /**
     * 本地插件
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function plugins(Request $request)
    {
        $where = [];
        $data  = Plugins::where($where)
            ->order('id desc')
            ->paginate()
            ->each(function ($item) {
                $item->plugin_name = "标识：{$item->name}";
                // 是否安装：20未安装 30已安装
                $item->plugin_state = $item->state === '20' ? '30' : '20';
                // 检测是否有更新
                if ($item->plugin_state === '30') {

                }
                // 是否有配置
                $item->plugin_config = '10';
                $setting = base_path("plugin/{$item->name}/setting/config.php");
                if (file_exists($setting)) {
                    $item->plugin_config = '20';
                }
            });
        return $this->successRes($data);
    }

    /**
     * 插件市场
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function store(Request $request)
    {
        return CloudSerivce::pluginList($request);
    }
}
