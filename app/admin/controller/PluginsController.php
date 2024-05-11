<?php

namespace app\admin\controller;

use app\builder\ListBuilder;
use app\model\Plugins;
use app\providers\PluginsProvider;
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
        $active  = $request->get('active', 'plugins');
        $data    = [
            [
                'label' => '我的插件',
                'value' => 'plugins',
            ],
            [
                'label' => '插件市场',
                'value' => 'stores',
            ],
        ];
        $builder = new ListBuilder;
        $builder->addActionOptions('操作', [
            'width' => 230
        ]);
        $builder->setTabs($data, 'active', $active);
        $builder->pageConfig();
        $builder->addRightButton('demo', '插件演示', [
            'type' => 'confirm',
            'api' => 'Plugins/demo',
            'method' => 'delete',
        ], [
            'type' => 'success',
            'title' => '温馨提示',
            'content' => '是否确认安装该插件数据？',
        ], [
            'type' => 'primary',
            'icon' => 'UploadFilled',
        ]);
        $builder->addRightButton('install', '安装插件', [
            'type' => 'confirm',
            'api' => 'Plugins/install',
            'method' => 'delete',
            'params' => [
                'field' => 'install',
                'value' => '10',
            ],
        ], [
            'type' => 'success',
            'title' => '温馨提示',
            'content' => '是否确认安装该插件数据？',
        ], [
            'type' => 'primary',
            'icon' => 'UploadFilled',
        ]);
        $builder->addRightButton('uninstall', '卸载插件', [
            'type' => 'confirm',
            'api' => 'Plugins/uninstall',
            'method' => 'delete',
            'params' => [
                'field' => 'install',
                'value' => '20',
            ],
        ], [
            'type' => 'error',
            'title' => '温馨提示',
            'content' => '是否确认卸载该插件数据？',
        ], [
            'type' => 'danger',
            'icon' => 'DeleteFilled',
        ]);
        if ($active == 'stores') {
            $builder->addColumn('create_at', '发布时间', [
                'width' => 160,
            ]);
        } else {
            $builder->addColumn('create_at', '安装时间', [
                'width' => 160,
            ]);
        }
        $builder->addColumnEle('plugins', '插件信息', [
            'width'     => 300,
            'params'    => [
                'type'  => 'pictuer',
                'image' => 'logo',
                'title' => 'title',
                'desc'  => 'name',
            ],
        ]);
        $builder->addColumn('author_name', '作者名称',[
            'width'     => 150,
        ]);
        $builder->addColumn('desc', '插件介绍', [
        ]);
        $builder->addColumn('version_name', '版本名称', [
            'width'     => 100
        ]);
        $builder->addColumn('version', '版本编号', [
            'width'     => 100
        ]);
        $builder->addColumn('down', '安装次数', [
            'width'     => 100
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
        $keyword = $request->get('page', '');
        $page    = (int) $request->get('page', 1);
        $limit   = (int) $request->get('limit', 20);
        if ($active == 'plugins') {
            $data = Plugins::order('id desc')->paginate([
                'page' => $page,
                'list_rows' => $limit,
            ])->toArray();
        } else {
            $data = PluginsProvider::getList($keyword, $page, $limit);
        }
        $list = $data['data'] ?? [];
        foreach ($list as &$value) {
            // 插件信息
            $value['title'] = "名称：{$value['title']}";
            $value['name'] = "标识：{$value['name']}";
            // 安装状态
            $value['install'] = '20';
        }
        $data['data'] = $list;
        return $this->successRes($data);
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
        $data = [];
        return $this->successRes($data);
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
        $data = [];
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
