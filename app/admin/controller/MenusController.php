<?php
namespace app\admin\controller;

use app\admin\view\MenuView;
use app\common\builder\FormBuilder;
use app\common\builder\table\RowEditTrait;
use app\common\providers\DictProvider;
use app\common\providers\MenuProvider;
use app\common\providers\RouteProvider;
use app\common\utils\FrameUtil;
use hg\apidoc\annotation as Apidoc;
use app\common\builder\ListBuilder;
use app\common\XbController;
use Webman\Event\Event;
use app\model\AdminRule;
use support\Request;

/**
 * 菜单管理
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class MenusController extends XbController
{
    // 引入可编辑行
    use RowEditTrait;

    /**
     * 模型
     * @var 
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
        $this->model = new AdminRule;
    }

    /**
     * 菜单记录-表格
     * @Apidoc\Method ("GET")
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function indexTable(Request $request)
    {
        $builder = new ListBuilder;
        $builder->addActionOptions('操作', [
            'width' => 200,
        ]);
        $builder->editConfig();
        $builder->treeConfig([
            'rowField' => 'id',
            'expandAll' => false,
        ]);
        $builder->addTopButton('add', '添加菜单', [
            'type' => 'modal',
            'api' => xbUrl('Menus/add'),
            'path' => xbUrl('Menus/add'),
        ], [
            'title' => '添加菜单',
            'customStyle' => [
                'width' => '550px',
                'height' => '75vh',
            ],
        ], [
            'type' => 'primary',
        ]);
        $builder->addRightButton('resources', '资源', [
            'type' => 'modal',
            'api' => xbUrl('Menus/resources'),
            'path' => xbUrl('Menus/resources'),
        ], [
            'title' => '生成资源菜单',
        ], [
            'type' => 'success',
            'icon' => 'Promotion',
        ]);
        $builder->addRightButton('edit', '修改', [
            'type' => 'modal',
            'api' => xbUrl('Menus/edit'),
            'path' => xbUrl('Menus/edit'),
        ], [
            'title' => '修改菜单',
            'customStyle' => [
                'width' => '550px',
                'height' => '75vh',
            ],
        ], [
            'type' => 'primary',
            'icon' => 'Edit',
        ]);
        $builder->addRightButton('del', '删除', [
            'type' => 'confirm',
            'api' => xbUrl('Menus/del'),
            'method' => 'delete',
        ], [
            'type' => 'error',
            'title' => '温馨提示',
            'content' => '子菜单也会将其删除，是否确认删除该数据？',
        ], [
            'type' => 'danger',
            'icon' => 'Close',
        ]);
        $builder->addColumn('title', '菜单名称', [
            'treeNode' => true,
        ]);
        $builder->addColumn('path', '路由地址');
        $builder->addColumnEle('type', '菜单类型', [
            'width' => 120,
            'params' => [
                'type' => 'tags',
                'options' => DictProvider::get('menuTypeText')->dict(),
                'style' => DictProvider::get('menuTypeStyle')->style(),
            ],
        ]);
        $builder->addColumn('methods', '请求类型', [
            'width' => 180,
        ]);
        $builder->addColumnEle('icon', '菜单图标', [
            'width' => 80,
            'params' => [
                'type' => 'icons',
            ],
        ]);
        $builder->addColumnEle('is_show', '是否显示', [
            'width' => 100,
            'params' => [
                'type' => 'switch',
                'api' => xbUrl('Menus/rowEdit'),
                'unchecked' => DictProvider::get('yesNoText')->switch('10'),
                'checked' => DictProvider::get('yesNoText')->switch('20'),
            ],
        ]);
        $builder->addColumnEle('component', '组件类型', [
            'width' => 120,
            'params' => [
                'type' => 'tags',
                'options' => DictProvider::get('componentText')->dict(),
                'style' => DictProvider::get('componentStyle')->style(),
            ],
        ]);
        $builder->addColumnEdit('sort', '排序', [
            'width' => 80,
            'params' => [
                'type' => 'input',
                'api' => xbUrl('Menus/rowEdit'),
                'min' => 0,
            ],
        ]);
        $data = $builder->create();
        return $this->successRes($data);
    }

    /**
     * 菜单列表
     * @Apidoc\Method ("GET")
     * @param \support\Request $request
     * @return mixed
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public function index(Request $request)
    {
        $data = AdminRule::order('sort asc,id asc')
            ->select();
        return $this->successRes($data);
    }

    /**
     * 添加菜单
     * @Apidoc\Method ("GET,POST")
     * @param \support\Request $request
     * @return mixed
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public function add(Request $request)
    {
        if ($request->method() === 'POST') {
            // 获取数据
            $post = $request->post();
            // 事件处理
            Event::dispatch('common.event.MenuEvent.add', $post);
            // 返回结果
            return $this->success('添加成功');
        }
        $builder = MenuView::formView();
        $builder->setMethod('POST');
        $data = $builder->create();
        return $this->successRes($data);
    }

    /**
     * 修改菜单
     * @Apidoc\Method ("GET,PUT")
     * @param \support\Request $request
     * @return mixed
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public function edit(Request $request)
    {
        $id = $request->get('id');
        $model = AdminRule::find((int) $id);
        if (!$model) {
            return $this->fail('数据不存在');
        }
        if ($request->method() === 'PUT') {
            // 获取数据
            $post = $request->post();
            $post['id'] = $id;
            // 事件处理
            Event::dispatch('common.event.MenuEvent.edit', $post);
            // 返回结果
            return $this->success('修改成功');
        }
        $data = $model->toArray();
        $data['methods'] = explode(',', $data['methods']);
        $builder = MenuView::formView();
        $builder->setMethod('PUT');
        $builder->setFormData($data);
        $data = $builder->create();
        return $this->successRes($data);
    }

    /**
     * 删除菜单
     * @Apidoc\Method ("GET")
     * @param \support\Request $request
     * @return mixed
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public function del(Request $request)
    {
        $id = (int) $request->post('id');
        // 事件处理
        Event::dispatch('common.event.MenuEvent.del', ['id' => $id]);
        // 返回数据
        return $this->success('删除成功');
    }

    /**
     * 资源菜单
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function resources(Request $request)
    {
        $id = (int) $request->get('id');
        $menu = AdminRule::find($id);
        if (!$menu) {
            return $this->fail('数据不存在');
        }
        if ($request->method() === 'POST') {
            $post = $request->post('result', []);
            if (empty($post)) {
                return $this->fail('请选择路由资源');
            }
            // 创建资源菜单
            MenuProvider::createResponse($menu->toArray(), $post);
            // 缓存路由
            RouteProvider::cacheMenus();
            // 重启服务
            FrameUtil::pcntlAlarm(2, function () {
                FrameUtil::reload();
            });
            // 返回数据
            return $this->success('生成资源菜单成功');
        }
        $options = MenuProvider::resourcesOption();
        foreach ($options as &$item) {
            // 检测是否表格
            if ($menu['component'] != 'table/index') {
                $item['disabled'] = true;
                continue;
            }
            $path = (string) $menu['path'];
            $path = explode('/', $path);
            $controler = $path[0] ?? '';
            if (empty($controler)) {
                return $this->fail('控制器数据错误');
            }
            if ($item['value'] === 'Table') {
                $fullPath = "{$controler}/index{$item['value']}";
            } else {
                $fullPath = "{$controler}/{$item['value']}";
            }
            $where = [
                ['pid', '=', $id],
                ['path', '=', "{$fullPath}"],
            ];
            $findData = AdminRule::where($where)->find();
            if ($findData) {
                $item['disabled'] = true;
            }
        }
        $builder = new FormBuilder;
        $builder->setMethod('POST');
        $builder->addRow('result', 'checkbox', '资源菜单', [], [
            'options' => $options,
        ]);
        $data = $builder->create();
        return $this->successRes($data);
    }
}
