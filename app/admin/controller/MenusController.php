<?php
namespace app\admin\controller;

use app\admin\validate\MenusValidate;
use app\admin\view\MenuView;
use app\common\builder\FormBuilder;
use app\common\builder\table\RowEditTrait;
use app\common\providers\DictProvider;
use app\common\providers\RouteProvider;
use app\common\utils\FrameUtil;
use hg\apidoc\annotation as Apidoc;
use app\common\builder\ListBuilder;
use app\model\AdminRule;
use support\Request;
use app\common\XbController;
use think\facade\Db;

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
     * 初始化
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function init()
    {
        parent::init();
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
        $data = $builder
            ->addActionOptions('操作', [
                'width' => 200,
            ])
            ->editConfig()
            ->treeConfig([
                'rowField' => 'id',
                'expandAll' => false,
            ])
            ->addTopButton('add', '添加菜单', [
                'type' => 'modal',
                'api' => xbUrl('Menus/add'),
                'path' => xbUrl('Menus/add'),
            ], [
                'title' => '添加菜单',
            ], [
                'type' => 'primary',
            ])
            ->addRightButton('resources', '资源', [
                'type' => 'modal',
                'api' => xbUrl('Menus/resources'),
                'path' => xbUrl('Menus/resources'),
            ], [
                'title' => '生成资源菜单',
            ], [
                'type' => 'success',
                'icon' => 'Promotion',
            ])
            ->addRightButton('edit', '修改', [
                'type' => 'modal',
                'api' => xbUrl('Menus/edit'),
                'path' => xbUrl('Menus/edit'),
            ], [
                'title' => '修改菜单',
            ], [
                'type' => 'primary',
                'icon' => 'Edit',
            ])
            ->addRightButton('del', '删除', [
                'type' => 'confirm',
                'api' => xbUrl('Menus/del'),
                'method' => 'delete',
            ], [
                'type' => 'error',
                'title' => '温馨提示',
                'content' => '是否确认删除该数据？',
            ], [
                'type' => 'danger',
                'icon' => 'Close',
            ])
            ->addColumn('title', '菜单名称', [
                'treeNode' => true,
            ])
            ->addColumn('path', '路由地址')
            ->addColumn('methods', '请求类型', [
                'width' => 180,
            ])
            ->addColumnEle('icon', '菜单图标', [
                'width' => 80,
                'params' => [
                    'type' => 'icons',
                ],
            ])
            ->addColumnEle('is_show', '是否显示', [
                'width' => 100,
                'params' => [
                    'type' => 'switch',
                    'api' => xbUrl('Menus/rowEdit'),
                    'unchecked' => DictProvider::get('yesNoText')->switch('10'),
                    'checked' => DictProvider::get('yesNoText')->switch('20'),
                ],
            ])
            ->addColumnEle('component', '组件类型', [
                'width' => 120,
                'params' => [
                    'type' => 'tags',
                    'options' => DictProvider::get('componentText')->dict(),
                    'style' => DictProvider::get('componentStyle')->style(),
                ],
            ])
            ->addColumnEdit('sort', '排序', [
                'width' => 80,
                'params' => [
                    'type' => 'input',
                    'api' => xbUrl('Menus/rowEdit'),
                    'min' => 0,
                ],
            ])
            ->create();
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
            // 数据验证
            xbValidate(MenusValidate::class, $post, 'add');
            // 获取父级ID
            $post['pid'] = is_array($post['pid']) ? end($post['pid']) : $post['pid'];
            $post['methods'] = implode(',', $post['methods']);
            // 开启事务
            Db::startTrans();
            try {
                $model = new AdminRule;
                if (!$model->save($post)) {
                    return $this->fail('添加失败');
                }
                // 提交事务
                Db::commit();
                // 缓存路由
                RouteProvider::cacheMenus();
                // 延迟重启
                FrameUtil::pcntlAlarm(2, function () {
                    // 重启服务
                    FrameUtil::reload();
                });
                // 返回结果
                return $this->success('添加成功');
            } catch (\Throwable $e) {
                Db::rollback();
                return $this->fail($e->getMessage());
            }
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
            // 数据验证
            xbValidate(MenusValidate::class, $post, 'edit');
            // 获取父级ID
            $post['pid'] = is_array($post['pid']) ? end($post['pid']) : $post['pid'];
            $post['methods'] = implode(',', $post['methods']);
            // 保存数据
            if (!$model->save($post)) {
                return $this->fail('修改失败');
            }
            // 缓存路由
            RouteProvider::cacheMenus();
            // 延迟重启
            FrameUtil::pcntlAlarm(2, function () {
                // 重启服务
                FrameUtil::reload();
            });
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
        $id = (int)$request->post('id');
        $model = AdminRule::find((int) $id);
        if (!$model) {
            return $this->fail('数据不存在');
        }
        if ($model['is_system'] === '20') {
            return $this->fail('系统菜单不允许删除');
        }
        if (!$model->delete()) {
            return $this->fail('删除失败');
        }
        // 缓存路由
        RouteProvider::cacheMenus();
        // 重启服务
        FrameUtil::pcntlAlarm(2, function () {
            FrameUtil::reload();
        });
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
        $id = (int)$request->get('id');
        $menu = AdminRule::find($id);
        if (!$menu) {
            return $this->fail('数据不存在');
        }
        if ($request->method() === 'POST') {
            $post = $request->post('result', []);
            $data = $this->getMenusChildren($post, $menu->toArray());
            if (empty($data)) {
                return $this->fail('请选择路由资源');
            }
            foreach ($data as $menuData) {
                $model = new AdminRule;
                if (!$model->save($menuData)) {
                    return $this->fail('生成资源菜单失败');
                }
            }
            // 缓存路由
            RouteProvider::cacheMenus();
            // 重启服务
            FrameUtil::pcntlAlarm(2, function () {
                FrameUtil::reload();
            });
            // 返回数据
            return $this->success('生成资源菜单成功');
        }
        $options = [
            [
                'label' => '添加',
                'value' => 'add',
                'disabled' => false,
            ],
            [
                'label' => '修改',
                'value' => 'edit',
                'disabled' => false,
            ],
            [
                'label' => '删除',
                'value' => 'del',
                'disabled' => false,
            ],
            [
                'label' => '表格',
                'value' => 'Table',
                'disabled' => false,
            ],
            [
                'label' => '修改列',
                'value' => 'rowEdit',
                'disabled' => false,
            ],
        ];
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
            'options' => $options
        ]);
        $data = $builder->create();
        return $this->successRes($data);
    }

    /**
     * 获取子级菜单数据
     * @param array $children
     * @param array $post
     * @param mixed $parentId
     * @return array|object
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function getMenusChildren(array $children, array $parent)
    {
        if (empty($children)) {
            return [];
        }
        $data = [];
        foreach ($children as $value) {
            // 删除多余字符串
            $parentPath = str_replace('/index', '', $parent['path']);
            $item = [
                'pid' => $parent['id'],
                'plugin_name' => $parent['plugin_name'],
                'module_name' => $parent['module_name'],
                'is_show' => '10',
                'is_default' => '10',
                'is_system' => '10',
                'icon' => '',
                'sort' => '0',
                'params' => '',
            ];
            // 添加
            if ($value === 'add') {
                $item['title'] = "{$parent['title']}-添加";
                $item['component'] = 'form/index';
                $item['path'] = "{$parentPath}/add";
                $item['methods'] = 'GET,POST';
                array_push($data, $item);
            }
            // 修改
            if ($value === 'edit') {
                $item['title'] = "{$parent['title']}-修改";
                $item['component'] = 'form/index';
                $item['path'] = "{$parentPath}/edit";
                $item['methods'] = 'GET,PUT';
                array_push($data, $item);
            }
            // 删除
            if ($value === 'del') {
                $item['title'] = "{$parent['title']}-删除";
                $item['component'] = 'none/index';
                $item['path'] = "{$parentPath}/del";
                $item['methods'] = 'DELETE';
                array_push($data, $item);
            }
            // 表格列
            if ($value === 'rowEdit') {
                $item['title'] = "{$parent['title']}-修改列";
                $item['component'] = 'none/index';
                $item['path'] = "{$parentPath}/rowEdit";
                $item['methods'] = 'GET,POST,PUT,DELETE';
                array_push($data, $item);
            }
        }
        return $data;
    }
}
