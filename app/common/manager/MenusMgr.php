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
                'width' => 180
            ])
            ->editConfig()
            ->treeConfig([
                'rowField' => 'id',
            ])
            ->addTopButton('add', '添加', [
                'api' => '/Menus/add',
                'path' => '/Menus/add'
            ], [], [
                'type' => 'primary'
            ])
            ->addRightButton('edit', '修改', [
                'api' => '/Menus/edit',
                'path' => '/Menus/edit'
            ], [], [
                'type' => 'primary',
            ])
            ->addRightButton('del', '删除', [
                'type' => 'confirm',
                'api' => '/Menus/del',
                'method' => 'delete',
            ], [
                'type' => 'error',
                'title' => '温馨提示',
                'content' => '是否确认删除该数据',
            ], [
                'type' => 'danger',
            ])
            ->addColumn('path', '菜单地址', [
                'treeNode' => true
            ])
            ->addColumn('title', '菜单名称')
            ->addColumnEle('icon', '菜单图标',[
                'width'     => 80,
                'params'    => [
                    'type'  => 'icons',
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
                'width' => 180
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
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function index(Request $request)
    {
        $data = MenusUtil::getMenus(null,true);
        return $this->successRes($data);
    }

    /**
     * 添加
     * @param \think\Request $request
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function add(Request $request)
    {
        $data = $this->formView()->create();
        return $this->successRes($data);
    }

    /**
     * 修改
     * @param \think\Request $request
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function edit(Request $request)
    {
        MenusUtil::save([]);
        $data = $this->formView()->create();
        return $this->successRes($data);
    }

    /**
     * 删除
     * @param \think\Request $request
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function del(Request $request)
    {
        return $this->successRes();
    }

    /**
     * 获取渲染视图
     * @return FormBuilder
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function formView()
    {
        $builder = new FormBuilder;
        $builder->addRow('title', 'input', '菜单名称', '', [
            'col' => 12,
        ])
            ->addRow('pid', 'cascader', '父级菜单', [0], [
                'props' => [
                    'props' => [
                        'checkStrictly' => true,
                    ],
                ],
                'options'       => MenusUtil::getCascaderOptions(),
                'placeholder'   => '如不选择则是顶级菜单',
                'col'           => 12,
            ])
            ->addRow('component', 'select', '菜单类型', 'none/index', [
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
                                    'span' => 12
                                ]),
                        ],
                    ],
                    [
                        'value' => ['', 'table/index', 'form/index'],
                        'where' => 'in',
                        'rule' => [
                            Elm::input('auth_params', '附带参数')
                                ->props([
                                    'placeholder' => '附带地址栏参数（选填），格式：name=楚羽幽&sex=男'
                                ])
                                ->col([
                                    'span' => 12
                                ]),
                        ],
                    ],
                ],
            ])
            ->addRow('path', 'input', '菜单路由', '', [
                'placeholder' => '示例：控制器/方法',
                'col' => 12,
            ])
            ->addRow('show', 'radio', '显示隐藏', '10', [
                'options' => ShowStatus::options(),
                'col' => 12,
            ])
            ->addRow('method', 'checkbox', '请求类型', ['GET'], [
                'options' => MethodsEnum::options(),
                'col' => 12,
            ])
            ->addRow('icon', 'icons', '菜单图标', '', [
                'col' => 12,
            ])
            ->addRow('sort', 'input', '菜单排序', '100', [
                'col' => 12,
            ]);
        return $builder;
    }
}
