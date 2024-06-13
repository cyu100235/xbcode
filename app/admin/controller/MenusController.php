<?php
namespace app\admin\controller;

use app\admin\validate\MenusValidate;
use app\common\builder\table\RowEditTrait;
use app\common\providers\DictProvider;
use app\common\providers\RouteProvider;
use app\common\utils\FrameUtil;
use hg\apidoc\annotation as Apidoc;
use app\common\builder\FormBuilder;
use app\common\builder\ListBuilder;
use app\model\AdminRule;
use app\common\providers\MenuProvider;
use FormBuilder\Factory\Elm;
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
     * 菜单模型
     * @var AdminRule
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
        $data    = $builder
            ->addActionOptions('操作', [
                'width' => 150,
            ])
            ->editConfig()
            ->treeConfig([
                'rowField' => 'id',
            ])
            ->addTopButton('add', '添加', [
                'type' => 'modal',
                'api' => xbUrl('Menus/add'),
                'path' => xbUrl('Menus/add'),
            ], [
                'title' => '添加菜单',
            ], [
                'type' => 'primary',
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
                'content' => '是否确认删除该数据',
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
        $model = $this->model;
        $data  = $model
            ->order('sort asc,id asc')
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
            $post['pid']     = is_array($post['pid']) ? end($post['pid']) : $post['pid'];
            $post['methods'] = implode(',', $post['methods']);
            // 是否有子级菜单
            $children = $post['children'];
            unset($post['children']);
            // 开启事务
            Db::startTrans();
            try {
                $model = $this->model;
                if (!$model->save($post)) {
                    return $this->fail('添加失败');
                }
                // 获取ID
                $post['id'] = $model->id;
                if ($post['component'] === 'table/index') {
                    $appendMenus = [
                        [
                            'pid' => $post['id'],
                            'title' => "{$post['title']}-表格",
                            'component' => 'none/index',
                            'path' => "{$post['path']}Table",
                            'class' => "{$post['class']}Table",
                            'methods' => 'GET',
                            'is_show' => '10',
                            'is_default' => '10',
                            'is_system' => '10',
                            'icon' => '',
                            'sort' => '0',
                            'auth_params' => '',
                        ]
                    ];
                    // 追加子级菜单
                    $appendMenus = array_merge($appendMenus, $this->getMenusChildren($children, $post));
                    // 批量保存菜单数据
                    $allModel = $this->model;
                    $allModel->saveAll($appendMenus);
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
        $builder  = $this->formView();
        $children = [
            [
                'label' => '添加',
                'value' => 'add',
            ],
            [
                'label' => '编辑',
                'value' => 'edit',
            ],
            [
                'label' => '删除',
                'value' => 'del',
            ],
        ];
        $builder->addRow('children', 'checkbox', '子级权限', [], [
            'col' => 12,
            'options' => $children,
        ]);
        $data = $builder->setMethod('POST')->create();
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
            $classPath  = str_replace('@index', '', $parent['class']);
            // 添加
            if ($value === 'add') {
                $item = [
                    'pid' => $parent['id'],
                    'title' => "{$parent['title']}-添加",
                    'component' => 'form/index',
                    'path' => "{$parentPath}/add",
                    'class' => "{$classPath}@add",
                    'methods' => 'GET,POST',
                    'is_show' => '10',
                    'is_default' => '10',
                    'is_system' => '10',
                    'icon' => '',
                    'sort' => '0',
                    'params' => '',
                ];
                array_push($data, $item);
            }
            // 修改
            if ($value === 'edit') {
                $item = [
                    'pid' => $parent['id'],
                    'title' => "{$parent['title']}-修改",
                    'component' => 'form/index',
                    'path' => "{$parentPath}/edit",
                    'class' => "{$classPath}@edit",
                    'methods' => 'GET,PUT',
                    'is_show' => '10',
                    'is_default' => '10',
                    'is_system' => '10',
                    'icon' => '',
                    'sort' => '0',
                    'params' => '',
                ];
                array_push($data, $item);
            }
            // 删除
            if ($value === 'del') {
                $item = [
                    'pid' => $parent['id'],
                    'title' => "{$parent['title']}-删除",
                    'component' => 'none/index',
                    'path' => "{$parentPath}/del",
                    'class' => "{$classPath}@del",
                    'methods' => 'DELETE',
                    'is_show' => '10',
                    'is_default' => '10',
                    'is_system' => '10',
                    'icon' => '',
                    'sort' => '0',
                    'params' => '',
                ];
                array_push($data, $item);
            }
        }
        return $data;
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
        $id    = $request->get('id');
        $model = $this->model;
        $model = $model->find((int) $id);
        if (!$model) {
            return $this->fail('数据不存在');
        }
        if ($request->method() === 'PUT') {
            // 获取数据
            $post = $request->post();
            // 数据验证
            xbValidate(MenusValidate::class, $post, 'edit');
            // 获取父级ID
            $post['pid']     = is_array($post['pid']) ? end($post['pid']) : $post['pid'];
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
        $data            = $model->toArray();
        $data['methods'] = explode(',', $data['methods']);
        $data            = $this->formView()
            ->setMethod('PUT')
            ->setFormData($data)
            ->create();
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
        $id    = $request->post('id');
        $model = $this->model;
        $model = $model->find((int) $id);
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
     * 获取渲染视图
     * @return FormBuilder
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    protected function formView()
    {
        $builder = new FormBuilder;
        $builder->addRow('title', 'input', '菜单名称', '', [
            'col' => 12,
        ]);
        $builder->addRow('pid', 'cascader', '父级菜单', [0], [
            'options' => MenuProvider::getCascaderOptions(),
            'placeholder' => '如不选择则是顶级菜单',
            'col' => 12,
            'props' => [
                'props' => [
                    'checkStrictly' => true,
                ],
            ],
        ]);
        $builder->addRow('component', 'select', '菜单类型', 'none/index', [
            'options' => DictProvider::get('componentText')->options(),
            'col' => 12,
            // 使用联动组件
            'control' => [
                [
                    'value' => 'remote/index',
                    'where' => '==',
                    'rule' => [
                        Elm::input('params', '远程组件')
                            ->props([
                                'placeholder' => '示例：remote/index，则会自动加载 http://www.xxx.com/remote/index.vue 文件作为组件渲染',
                            ])
                            ->col([
                                'span' => 12,
                            ]),
                    ],
                ],
                [
                    'value' => 'remote/index',
                    'where' => '!=',
                    'rule' => [
                        Elm::input('params', '附带参数')
                            ->props([
                                'placeholder' => '附带地址栏参数（选填），格式：name=楚羽幽&sex=男',
                            ])
                            ->col([
                                'span' => 12,
                            ]),
                    ],
                ],
            ],
        ]);
        $builder->addRow('path', 'input', '菜单路由', '', [
            'col' => 12,
        ]);
        $builder->addRow('class', 'input', '执行方法', '', [
            'col' => 24,
            'prompt' => '命名空间类@方法名，示例：\plugin\user\controller\IndexController@index',
        ]);
        $builder->addRow('is_show', 'radio', '显示隐藏', '10', [
            'options' => DictProvider::get('showText')->options(),
            'col' => 12,
        ]);
        $builder->addRow('methods', 'checkbox', '请求类型', ['GET'], [
            'options' => DictProvider::get('methodsText')->options(),
            'col' => 12,
        ]);
        $builder->addRow('icon', 'icons', '菜单图标', '', [
            'col' => 12,
        ]);
        $builder->addRow('sort', 'input', '菜单排序', '0', [
            'col' => 12,
        ]);
        return $builder;
    }
}
