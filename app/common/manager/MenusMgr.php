<?php
namespace app\common\manager;

use app\common\builder\FormBuilder;
use app\common\builder\ListBuilder;
use app\common\enum\MenuComponent;
use app\common\enum\MenuComponentStyle;
use app\common\enum\MethodsEnum;
use app\common\enum\ShowStatus;
use app\common\enum\ShowStatusStyle;
use app\common\utils\JsonUtil;
use app\common\utils\MenusUtil;
use app\common\validate\MenusValidate;
use FormBuilder\Factory\Elm;
use think\Request;

trait MenusMgr
{
    use JsonUtil;

    /**
     * 项目ID
     * @var int
     */
    protected $saas_appid = null;

    /**
     * 表格列
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function indexTable(Request $request)
    {
        $builder = new ListBuilder;
        $data    = $builder
            ->addActionOptions('操作', [
                'width' => 180,
            ])
            ->editConfig()
            ->treeConfig([
                'rowField'  => 'id',
            ])
            ->addTopButton('add', '添加', [
                'type'      => 'modal',
                'api'       => '/Menus/add',
                'path'      => '/Menus/add',
            ], [
                'title'     => '添加菜单',
            ], [
                'type'      => 'primary',
            ])
            ->addRightButton('edit', '修改', [
                'type'      => 'modal',
                'api'       => '/Menus/edit',
                'path'      => '/Menus/edit',
            ], [
                'title'     => '修改菜单',
            ], [
                'type'      => 'primary',
            ])
            ->addRightButton('del', '删除', [
                'type'      => 'confirm',
                'api'       => '/Menus/del',
                'method'    => 'delete',
            ], [
                'type'      => 'error',
                'title'     => '温馨提示',
                'content'   => '是否确认删除该数据',
            ], [
                'type' => 'danger',
            ])
            ->addColumn('title', '菜单名称', [
                'treeNode' => true,
            ])
            ->addColumn('path', '菜单地址')
            ->addColumnEle('icon', '菜单图标', [
                'width' => 80,
                'params' => [
                    'type' => 'icons',
                ],
            ])
            ->addColumnEle('show', '是否显示', [
                'width' => 100,
                'params' => [
                    'type' => 'tags',
                    'options' => ShowStatus::dict(),
                    'style' => ShowStatusStyle::labelMap('type'),
                ],
            ])
            ->addColumnEle('component', '组件类型', [
                'width' => 120,
                'params' => [
                    'type' => 'tags',
                    'options' => MenuComponent::dict(),
                    'style' => MenuComponentStyle::labelMap('type', false),
                ],
            ])
            ->addColumn('method', '请求类型', [
                'width' => 180,
            ])
            ->addColumnEdit('sort', '排序', [
                'width' => 100,
                'params' => [
                    'type' => 'input',
                    'api' => $request->moduleName . '/admin/Menus/rowEdit',
                    'min' => 0,
                ],
            ])
            ->create();
        return $this->successRes($data);
    }

    /**
     * 列表数据
     * @param \think\Request $request
     * @return mixed
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public function index(Request $request)
    {
        $data = MenusUtil::getMenus(true);
        return $this->successRes($data);
    }


    /**
     * 编辑行数据
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function rowEdit(Request $request)
    {
        # 需要修改的ID
        $id = $request->post('id');
        # 查询键
        $keyField = $request->post('keyField');
        # 修改键
        $field = $request->post('field');
        # 修改值
        $value = $request->post('value');
        # 获取列表
        $data = MenusUtil::getMenus(true);
        # 查询数据
        $arrayIndex = array_search($id, array_column($data, $keyField));
        # 检测并判断数据
        $item = isset($data[$arrayIndex]) ? $data[$arrayIndex] : [];
        if (empty($item)) {
            return $this->fail('数据不存在');
        }
        $data[$arrayIndex][$field] = $value;
        # 保存菜单数据
        MenusUtil::saveMenusData($data);
        # 返回结果
        return $this->success('修改成功');
    }

    /**
     * 添加
     * @param \think\Request $request
     * @return mixed
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public function add(Request $request)
    {
        if ($request->isPost()) {
            # 获取数据
            $post = $request->post();
            # 数据验证
            xbValidate(MenusValidate::class, $post,'add');
            $children = $post['children'];
            unset($post['children']);
            # 保存数据
            $pid = MenusUtil::save($post,null);
            # 保存子级菜单
            $post['id'] = $pid;
            # 表格组件额外新增表格规则
            $appendMenus = [];
            if ($post['component'] === 'table/index') {
                $tableRule = [
                    'pid'           => $pid,
                    'title'         => "{$post['title']}-表格规则",
                    'component'     => 'none/index',
                    'path'          => "{$post['path']}Table",
                    'show'          => '10',
                    'method'        => ['GET'],
                    'icon'          => '',
                    'is_default'    => '10',
                    'sort'          => '100',
                    'auth_params'   => '',
                    'is_system'     => '10',
                ];
                array_push($appendMenus, $tableRule);
            }
            $appendMenus = array_merge($appendMenus, $this->getMenusChildren($children, $post));
            # 批量保存
            MenusUtil::saveAll($appendMenus);
            # 返回结果
            return $this->success('添加成功');
        }
        $builder = $this->formView();
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
     * @param array $parent
     * @return array|object|null
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    private function getMenusChildren(array $children,array $parent)
    {
        if (empty($children)) {
            return [];
        }
        $data = [];
        foreach ($children as $value) {
            $parentPath = explode('/',$parent['path']);
            # 添加
            if ($value === 'add') {
                $row = end($data);
                $item   = [
                    'pid'           => $parent['id'],
                    'title'         => "{$parent['title']}-添加",
                    'component'     => 'form/index',
                    'is_api'        => '20',
                    'path'          => "{$parentPath[0]}/add",
                    'show'          => '10',
                    'method'        => ['GET','POST'],
                    'icon'          => '',
                    'is_default'    => '10',
                    'sort'          => '100',
                    'auth_params'   => '',
                    'is_system'     => '10',
                ];
                array_push($data, $item);
            }
            # 修改
            if ($value === 'edit') {
                $row = end($data);
                $item   = [
                    'pid'           => $parent['id'],
                    'title'         => "{$parent['title']}-修改",
                    'component'     => 'form/index',
                    'is_api'        => '20',
                    'path'          => "{$parentPath[0]}/edit",
                    'show'          => '10',
                    'method'        => ['GET','PUT'],
                    'icon'          => '',
                    'is_default'    => '10',
                    'sort'          => '100',
                    'auth_params'   => '',
                    'is_system'     => '10',
                ];
                array_push($data, $item);
            }
            # 删除
            if ($value === 'del') {
                $row = end($data);
                $item   = [
                    'pid'           => $parent['id'],
                    'title'         => "{$parent['title']}-删除",
                    'component'     => 'none/index',
                    'is_api'        => '20',
                    'path'          => "{$parentPath[0]}/del",
                    'show'          => '10',
                    'method'        => ['GET','DELETE'],
                    'icon'          => '',
                    'is_default'    => '10',
                    'sort'          => '100',
                    'auth_params'   => '',
                    'is_system'     => '10',
                ];
                array_push($data, $item);
            }
        }
        return $data;
    }

    /**
     * 修改
     * @param \think\Request $request
     * @return mixed
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public function edit(Request $request)
    {
        $id = $request->get('id');
        $detail = MenusUtil::find((int) $id);
        if ($request->isPut()) {
            # 获取数据
            $post = $request->post();
            # 数据验证
            xbValidate(MenusValidate::class, $post,'edit');
            # 保存数据
            MenusUtil::save($post,$id);
            # 返回结果
            return $this->success('修改成功');
        }
        $data = $this->formView()
        ->setMethod('PUT')
        ->setFormData($detail)
        ->create();
        return $this->successRes($data);
    }

    /**
     * 删除
     * @param \think\Request $request
     * @return mixed
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public function del(Request $request)
    {
        $id = $request->post('id');
        MenusUtil::del((int) $id);
        return $this->success('删除成功');
    }

    /**
     * 获取渲染视图
     * @return FormBuilder
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public function formView()
    {
        $builder = new FormBuilder;
        $builder->addRow('title', 'input', '菜单名称', '', [
            'col' => 12,
        ]);
        $builder->addRow('pid', 'cascader', '父级菜单', [0], [
            'props' => [
                'props' => [
                    'checkStrictly' => true,
                ],
            ],
            'options' => MenusUtil::getCascaderOptions(),
            'placeholder' => '如不选择则是顶级菜单',
            'col' => 12,
        ]);
        $builder->addRow('component', 'select', '菜单类型', 'none/index', [
            'options' => MenuComponent::options(),
            'col' => 12,
            // 使用联动组件
            'control' => [
                [
                    'value' => 'remote/index',
                    'where' => '==',
                    'rule' => [
                        Elm::input('auth_params', '远程组件')
                            ->props([
                                'placeholder' => '示例：remote/index，则会自动加载 http://www.xxx.com/remote/index.vue 文件作为组件渲染',
                            ])
                            ->col([
                                'span' => 12,
                            ]),
                    ],
                ],
                [
                    'value' => ['', 'table/index', 'form/index'],
                    'where' => 'in',
                    'rule' => [
                        Elm::input('auth_params', '附带参数')
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
            'placeholder' => '示例：控制器/方法',
            'col' => 12,
        ]);
        $builder->addRow('show', 'radio', '显示隐藏', '10', [
            'options' => ShowStatus::options(),
            'col' => 12,
        ]);

        $builder->addRow('method', 'checkbox', '请求类型', ['GET'], [
            'options' => MethodsEnum::options(),
            'col' => 12,
        ]);
        $builder->addRow('icon', 'icons', '菜单图标', '', [
            'col' => 12,
        ]);
        $builder->addRow('sort', 'input', '菜单排序', '100', [
            'col' => 12,
        ]);
        return $builder;
    }
}
