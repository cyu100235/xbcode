<?php
namespace app\backend\controller;

use Exception;
use support\Request;
use xbcode\XbController;
use app\model\AdminRule;
use xbcode\utils\DataUtil;
use xbcode\builder\FormBuilder;
use xbcode\builder\ListBuilder;
use xbcode\providers\DictProvider;
use xbcode\providers\MenuProvider;
use app\validate\AdminRuleValidate;
use xbcode\builder\table\attrs\RowEditTrait;

/**
 * 菜单管理
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class AdminRuleController extends XbController
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
     * 表格
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function indexTable(Request $request)
    {
        $builder = new ListBuilder;
        $builder->addActionOptions('操作', [
            'width' => 230,
        ]);
        $builder->editConfig();
        $builder->treeConfig([
            'rowField' => 'id',
            'expandAll' => false,
        ]);
        $builder->addTopButton('add', '添加菜单', [
            'type' => 'modal',
            'api' => xbUrl('AdminRule/add'),
            'path' => xbUrl('AdminRule/add'),
        ], [
            'title' => '添加菜单',
            'customStyle' => [
                'width' => '500px',
                'height' => '70vh',
            ],
        ], [
            'type' => 'primary',
        ]);
        $builder->addRightButton('edit', '资源', [
            'type' => 'modal',
            'api' => xbUrl('AdminRule/resources'),
            'path' => xbUrl('AdminRule/resources'),
        ], [
            'title' => '资源菜单',
            'customStyle' => [
                'width' => '500px',
            ]
        ], [
            'type' => 'success',
            'icon' => 'Edit',
        ]);
        $builder->addRightButton('edit', '修改', [
            'type' => 'modal',
            'api' => xbUrl('AdminRule/edit'),
            'path' => xbUrl('AdminRule/edit'),
        ], [
            'title' => '修改菜单',
            'customStyle' => [
                'width' => '500px',
                'height' => '70vh',
            ],
        ], [
            'type' => 'primary',
            'icon' => 'Edit',
        ]);
        $builder->addRightButton('del', '删除', [
            'type' => 'confirm',
            'api' => xbUrl('AdminRule/del'),
            'path' => xbUrl('AdminRule/del'),
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
        $builder->addColumn('type', '菜单类型', [
            'width' => 120,
            'params' => [
                'type' => 'dict',
                'props' => [
                    'options' => DictProvider::get('menuTypeText')->dict(),
                    'style' => DictProvider::get('menuTypeStyle')->style(),
                ],
            ],
        ]);
        $builder->addColumn('method', '请求类型', [
            'width' => 180,
            'params' => [
                'type' => 'tag',
            ],
        ]);
        $builder->addColumn('icon', '菜单图标', [
            'width' => 80,
            'params' => [
                'type' => 'icon',
            ],
        ]);
        $builder->addColumn('is_show', '是否显示', [
            'width' => 100,
            'params' => [
                'type' => 'switch',
                'api' => xbUrl('AdminRule/rowEdit'),
                'props' => [
                    'activeText' => '开启',
                    'inactiveText' => '关闭',
                    'activeValue' => '20',
                    'inactiveValue' => '10',
                ],
            ],
        ]);
        $builder->addColumn('component', '组件类型', [
            'width' => 120,
            'params' => [
                'type' => 'dict',
                'props' => [
                    'options' => DictProvider::get('componentText')->dict(),
                    'style' => DictProvider::get('componentStyle')->style(),
                ],
            ],
        ]);
        $builder->addColumn('sort', '排序', [
            'width' => 80,
            'params' => [
                'type' => 'input',
                'api' => xbUrl('AdminRule/rowEdit'),
                'min' => 0,
            ],
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
        $where = [
            ['plugin', '=', ''],
        ];
        $data  = AdminRule::where($where)
            ->order('sort asc,id asc')
            ->select()
            ->toArray();
        $data  = array_map(function ($item) {
            if (is_array($item['method'])) {
                $item['method'] = implode(',', $item['method']);
            }
            return $item;
        }, $data);
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
        if ($request->method() === 'POST') {
            // 获取数据
            $post = $request->post();
            // 数据验证
            xbValidate(AdminRuleValidate::class, $post, 'add');
            // 获取父级ID
            if (is_array($post['pid'])) {
                $post['pid'] = end($post['pid']);
            }
            if (!empty($post['remote']) && $post['component'] === 'remote/index') {
                $post['params'] = $post['remote'];
            }
            // 保存数据
            $model = new AdminRule;
            if (!$model->save($post)) {
                throw new Exception('添加菜单失败');
            }
            // 返回结果
            return $this->success('添加成功');
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
        $id    = (int) $request->get('id');
        $model = AdminRule::find($id);
        if (!$model) {
            return $this->fail('数据不存在');
        }
        if ($request->method() === 'PUT') {
            // 获取数据
            $post = $request->post();
            // 数据验证
            xbValidate(AdminRuleValidate::class, $post, 'edit');
            // 获取父级ID
            if (is_array($post['pid'])) {
                $post['pid'] = end($post['pid']);
            }
            if (!empty($post['remote']) && $post['component'] === 'remote/index') {
                $post['params'] = $post['remote'];
            }
            if (!$model->save($post)) {
                throw new Exception('编辑失败');
            }
            // 返回结果
            return $this->success('修改成功');
        }
        $data           = $model->toArray();
        $data['remote'] = $data['params'];
        $builder        = $this->formView();
        $builder->setMethod('PUT');
        $builder->setFormData($data);
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
        $id = (int) $request->post('id');
        // 递归查询子菜单ID
        $ids = MenuProvider::getChildrenIds($id);
        // 批量删除
        $data = AdminRule::where('id', 'in', $ids)->select();
        foreach ($data as $model) {
            if (!$model->delete()) {
                throw new Exception("ID:{$model['id']} 删除失败");
            }
        }
        // 返回数据
        return $this->success('删除成功');
    }

    /**
     * 资源菜案
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function resources(Request $request)
    {
        $id   = (int) $request->get('id');
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
            $path    = (string) $menu['path'];
            $path    = explode('/', $path);
            $module  = $path[0] ?? '';
            $control = $path[1] ?? '';
            if (empty($control)) {
                return $this->fail('控制器数据错误');
            }
            if ($item['value'] === 'Table') {
                $fullPath = "{$module}/{$control}/index{$item['value']}";
            } else {
                $fullPath = "{$module}/{$control}/{$item['value']}";
            }
            $where    = [
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
        $builder->addRow('description', 'xbAlert', '', '', [
            'props' => [
                'title' => '温馨提示',
                'type' => 'success',
                'closable' => false,
                'content' => '父级菜单为表格时，才能生成资源菜单',
            ],
        ]);
        $data = $builder->create();
        return $this->successRes($data);
    }

    /**
     * 显示表单
     * @return FormBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function formView()
    {
        $builder = new FormBuilder;
        $rule    = $this->menuTypeRule();
        $builder->addControl('type', 'radio', '菜单类型', '10', $rule, [
            'options' => DictProvider::get('menuTypeText')->options(),
        ]);
        $builder->addRow('pid', 'cascader', '父级菜单', [0], [
            'options' => self::getCascaderOptions(),
            'prompt' => '如不选择，则是顶级菜单',
            'props' => [
                'props' => [
                    'checkStrictly' => true,
                ],
            ],
        ]);
        $builder->addRow('title', 'input', '菜单名称', '', [
            'prompt' => '左侧菜单名称，顶级菜单时，尽可能2个字'
        ]);
        $rule = $this->menuComponentType();
        $builder->addControl('component', 'select', '菜单组件', 'none/index', $rule, [
            'options' => DictProvider::get('componentText')->options(),
            'prompt' => '选择顶级菜单时，默认即可',
        ]);
        $builder->addRow('path', 'input', '菜单地址', '', [
            'prompt' => '顶级菜单示例：workbench<br />子菜单示例：admin/Index/index，对应：模块/控制器/方法<br />注意！区分大小写',
        ]);
        $builder->addRow('params', 'input', '附带参数', '', [
            'prompt' => '（选填）地址栏参数，示例：name=楚羽幽&sex=男',
        ]);
        $builder->addRow('remote', 'input', '远程组件', '', [
            'prompt' => '示例：admin/Index/workbench，则请求该接口获取组件渲染内容',
        ]);
        $builder->addRow('method', 'checkbox', '请求类型', ['GET'], [
            'options' => DictProvider::get('methodsText')->options(),
        ]);
        $builder->addRow('icon', 'xbIcon', '菜单图标', '', [
            'col' => 12,
        ]);
        $builder->addRow('is_show', 'radio', '显示隐藏', '10', [
            'col' => 12,
            'options' => DictProvider::get('showText')->options(),
        ]);
        return $builder;
    }

    /**
     * 获取多级选项
     * @return array<int|string>[]
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function getCascaderOptions()
    {
        $where = [
            ['plugin', '=', ''],
        ];
        $data = AdminRule::where($where)->order('sort asc,id asc')->select()->toArray();
        $data = DataUtil::channelLevel($data, 0, '', 'id', 'pid');
        $data = MenuProvider::getChildrenOptions($data);
        $data = array_merge([
            [
                'label' => '顶级权限菜单',
                'value' => 0
            ]
        ], $data);
        return $data;
    }

    /**
     * 菜单类型规则
     * @return array[]
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function menuTypeRule()
    {
        return [
            // 目录
            [
                'value' => '10',
                'rule' => [
                    'pid',
                    'title',
                    'path',
                    'icon',
                    'is_show',
                ],
            ],
            // 菜单
            [
                'value' => '20',
                'rule' => [
                    'pid',
                    'title',
                    'path',
                    'icon',
                    'is_show',
                    'component',
                    'params',
                    'plugin_name',
                    'module_name',
                    'methods',
                    'icon',
                    'is_show',
                ],
            ],
            // 按钮
            [
                'value' => '30',
                'rule' => [
                    'pid',
                    'title',
                    'path',
                    'component',
                    'params',
                    'plugin_name',
                    'module_name',
                    'methods',
                ],
            ],
        ];
    }

    /**
     * 菜单组件类型规则
     * @return array[]
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function menuComponentType()
    {
        return [
            // 附带参数
            [
                'value' => 'remote/index',
                'condition' => '!=',
                'rule' => [
                    'params',
                ],
            ],
            // 远程组件
            [
                'value' => 'remote/index',
                'condition' => '==',
                'rule' => [
                    'remote',
                ],
            ],
        ];
    }
}
