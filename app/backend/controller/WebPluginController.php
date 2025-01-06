<?php
namespace app\backend\controller;

use support\Request;
use app\model\Plugins;
use app\model\WebPlugin;
use xbcode\XbController;
use xbcode\builder\FormBuilder;
use xbcode\builder\ListBuilder;
use app\validate\WebPluginValidate;

/**
 * 站点插件管理
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class WebPluginController extends XbController
{
    /**
     * 模型
     * @var WebPlugin
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
        $this->model = new WebPlugin;
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
        $siteId = $request->get('site_id');
        // 表格渲染
        $builder = new ListBuilder;
        $builder->addActionOptions('操作', [
            'width' => 180
        ]);
        $builder->pageConfig();
        $builder->addTopButton('add', '添加插件', [
            'type' => 'modal',
            'api' => xbUrl('WebPlugin/add'),
            'path' => xbUrl('WebPlugin/add'),
            'queryParams' => [
                'site_id' => $siteId,
            ],
        ], [
            'title' => '添加插件授权',
        ], [
            'type' => 'primary',
            'icon' => 'Plus'
        ]);
        $builder->addRightButton('edit', '修改', [
            'type' => 'modal',
            'api' => xbUrl('WebPlugin/edit'),
            'path' => xbUrl('WebPlugin/edit'),
            'queryParams' => [
                'site_id' => $siteId,
            ],
        ], [
            'title' => '修改插件授权',
        ], [
            'type' => 'primary',
            'icon' => 'EditOutlined'
        ]);
        $builder->addRightButton('del', '删除', [
            'type' => 'confirm',
            'api' => xbUrl('WebPlugin/del'),
            'path' => xbUrl('WebPlugin/del'),
            'method' => 'DELETE',
        ], [
            'type' => 'error',
            'title' => '温馨提示',
            'content' => '是否确认删除该数据？',
        ], [
            'type' => 'danger',
            'icon' => 'DeleteOutlined'
        ]);
        $builder->addColumn('title', '插件名称', [
            'minWidth' => 180,
        ]);
        $builder->addColumn('name', '插件标识', [
            'minWidth' => 180,
        ]);
        $builder->addColumn('expire_time', '过期时间', [
            'width' => 180,
        ]);
        $builder->addColumn('create_at', '创建时间', [
            'width' => 180,
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
        $siteId = $request->get('site_id');
        $model  = $this->model;
        $where  = [
            'saas_appid' => $siteId,
        ];
        $pluginModel = new Plugins;
        $plugins = $pluginModel->pluginCacheDict();
        $plugins = array_column($plugins, null, 'name');
        $data   = $model
            ->where($where)
            ->order('id desc')
            ->paginate()
            ->each(function ($item)use($plugins) {
                // 设置插件名称
                $plugin      = $plugins[$item->name] ?? [];
                $item->title = $plugin['title'] ?? '未安装插件';
                // 设置过期时间
                if (empty($item->expire_time)) {
                    $item->expire_time = '永久不过期';
                }
            });
        return $this->successRes($data);
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
            $siteId          = $request->get('site_id');
            $post            = $request->post();
            $post['saas_appid'] = $siteId;

            // 数据验证
            xbValidate(WebPluginValidate::class, $post, 'add');
            // 设置永久不到期
            if (empty($post['expire_time'])) {
                unset($post['expire_time']);
            }
            $model = $this->model;
            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            // 更新缓存
            WebPlugin::getWebAuthPlugin(true);
            // 返回数据
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
        $model = $this->model;
        $model = $model->where('id', $id)->find();
        if (!$model) {
            return $this->fail('该数据不存在');
        }
        if ($request->method() == 'PUT') {
            $post = $request->post();

            // 数据验证
            xbValidate(WebPluginValidate::class, $post, 'edit');
            // 设置永久不到期
            if (empty($post['expire_time'])) {
                unset($post['expire_time']);
            }
            
            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            // 更新缓存
            WebPlugin::getWebAuthPlugin(true);
            // 返回数据
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
        $model = $this->model;
        $model = $model->where('id', $id)->find();
        if (!$model) {
            return $this->fail('该数据不存在');
        }
        if (!$model->delete()) {
            return $this->fail('删除失败');
        }
        // 更新缓存
        WebPlugin::getWebAuthPlugin(true);
        // 返回数据
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
        $plugin        = new Plugins;
        $pluginOptions = $plugin->pluginCacheDict(true);
        $pluginOptions = array_filter($pluginOptions, function ($item) {
            return $item['state'] == '20';
        });
        $pluginOptions = array_values($pluginOptions);
        $pluginOptions = array_map(function ($item) {
            return [
                'value' => $item['name'],
                'label' => $item['title'],
            ];
        }, $pluginOptions);
        $disabled      = request()->get('id');
        $builder       = new FormBuilder;
        $builder->addRow('name', 'select', '选择插件', '', [
            'options' => $pluginOptions,
            'disabled' => $disabled ? true : false,
        ]);
        $builder->addRow('expire_time', 'datePicker', '到期时间', '', [
            'prompt' => '过期时间，不填写则永久，示例：2021-12-31',
        ]);
        return $builder;
    }
}
