<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @version  1.0
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\app\admin\controller;

use Exception;
use support\Request;
use plugin\xbCode\XbController;
use plugin\xbCode\enum\ShowEnum;
use plugin\xbCode\enum\StateEnum;
use plugin\xbCode\utils\DataUtil;
use plugin\xbCode\api\MenuOption;
use plugin\xbCode\builder\Builder;
use plugin\xbCode\enum\MethodEnum;
use plugin\xbCode\api\MenuChecked;
use plugin\xbCode\enum\MenuTypeEnum;
use plugin\xbCode\app\model\AdminRule;
use plugin\xbCode\builder\Renders\Form;
use plugin\xbCode\builder\Renders\Grid;
use plugin\xbCode\builder\Components\Form\InputKV;
use plugin\xbCode\app\validate\AdminRuleValidate;

/**
 * 菜单管理
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class AdminRuleController extends XbController
{
    /**
     * 表格
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        $act = (int) $request->get('_act');
        if ($act) {
            $where = [
                'is_saas' => 10,
            ];
            $data = AdminRule::where($where)->order('sort asc,id asc')->select()->toArray();
            $data = MenuChecked::menu2DToTree($data);
            $data = MenuChecked::unsetMenusFields($data, [
                '_html',
                '_level',
            ]);
            return $this->successData($data);
        }
        $builder = Builder::crud(function (Grid $builder) {
            $builder->useCRUD()->expandConfig([
                'expand' => 'all',
                'expandAll' => true,
            ]);
            $builder->setCRUDActionConfig('width', 150);
            $builder->addHeaderDialogBtn('添加菜单', xbUrl('AdminRule/add'), [
                'dialog' => [
                    'title' => '添加菜单',
                    'size' => 'lg',
                ],
            ])->level('primary');
            $builder->addActionDialogBtn('修改', xbUrl('AdminRule/edit'), [
                'dialog' => [
                    'title' => '修改菜单',
                    'size' => 'lg',
                ],
            ])
                ->disabledTip('系统菜单，禁止修改')
                ->disabledOn('this.is_system == 20')
                ->level('primary');
            $builder->addActionConfirmBtn('删除', xbUrl('AdminRule/del'))
                ->disabledTip('系统菜单，禁止删除')
                ->disabledOn('this.is_system == 20')
                ->level('danger');

            // 设置表格列快速编辑
            $builder->useCRUD()->quickSaveItemApi(xbUrl('AdminRule/rowEdit'));
            // 添加表格列
            $builder->addColumn('title', '菜单名称');
            $builder->addColumn('plugin', '插件标识');
            $builder->addColumn('path', '路由地址');
            $builder->addColumnMap('type', '菜单类型', MenuTypeEnum::dict());
            $builder->addColumn('method', '请求类型');
            $builder->addColumnIcon('icon', '菜单图标');
            $builder->addColumnMap('state', '是否启用', StateEnum::dict([
                '10' => "<span class='label label-danger'>{value}</span>",
                '20' => "<span class='label label-success'>{value}</span>",
            ]));
            $builder->addColumnMap('is_show', '是否显示', ShowEnum::dict());
            $builder->addColumnInput('sort', '菜单排序')->width(100);
        });
        return $this->successRes($builder);
    }

    /**
     * 快速编辑
     * @param \support\Request $request
     * @throws \Exception
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function rowEdit(Request $request)
    {
        $id = (int) $request->post('id');
        $post = $request->post();
        // 获取数据
        $model = AdminRule::find($id);
        if (!$model) {
            return $this->fail('数据不存在');
        }
        if (!$model->save($post)) {
            throw new Exception('修改失败');
        }
        return $this->success('修改成功');
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
            $isWeb = $request->saas_appid ? '20' : '10';
            // 数据验证
            xbValidate(AdminRuleValidate::class, $post, 'add');
            // 设置是否Sass菜单
            $post['is_saas'] = $isWeb;
            // 设置父级菜单
            $post['pid'] = isset($post['pid']['value']) ? $post['pid']['value'] : $post['pid'];
            // 附带参数
            $post['params'] = http_build_query($post['params'] ?? []);
            // 请求类型全部转小写
            $post['method'] = is_array($post['method']) ? implode(',', $post['method']) : 'get';
            // 转大写
            $post['method'] = strtoupper($post['method']);
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
        return $this->successRes($builder);
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
        $id = (int) $request->get('id');
        $model = AdminRule::find($id);
        if (!$model) {
            return $this->fail('数据不存在');
        }
        if ($request->method() === 'PUT') {
            // 获取数据
            $post = $request->post();
            // 数据验证
            xbValidate(AdminRuleValidate::class, $post, 'edit');
            // 设置父级菜单
            $post['pid'] = isset($post['pid']['value']) ? $post['pid']['value'] : $post['pid'];
            // 附带参数
            $params = $post['params'] ?? [];
            $post['params'] = http_build_query(is_array($params) ? $params : []);
            // 请求类型全部转小写
            $post['method'] = is_array($post['method']) ? implode(',', $post['method']) : 'GET';
            $post['method'] = strtoupper($post['method']);
            if (!$model->save($post)) {
                throw new Exception('编辑失败');
            }
            // 返回结果
            return $this->success('修改成功');
        }
        $data = $model->toArray();
        if ($data['is_system'] == '20') {
            return $this->fail('系统菜单，禁止操作');
        }
        parse_str($data['params'], $data['params']);
        // 处理请求类型
        $methods = is_array($data['method']) ? implode(',', $data['method']) : $data['method'];
        // 全部转小写
        $methods = strtolower($methods);
        $data['method'] = $methods;
        // 设置表单
        $builder = $this->formView();
        $builder->setMethod('PUT');
        $builder->setData($data);
        return $this->successRes($builder);
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
        $id = (int) $request->get('id');
        $model = AdminRule::where('id', $id)->find();
        if (!$model) {
            return $this->fail('数据不存在');
        }
        if ($model['is_system'] == '20') {
            return $this->fail('系统菜单，禁止操作');
        }
        if (!$model->delete()) {
            throw new Exception("ID:{$model['id']} 删除失败");
        }
        // 返回数据
        return $this->success('删除成功');
    }

    /**
     * 获取表单视图
     * @return Form
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function formView()
    {
        $builder = Builder::form(function (Form $builder) {
            $builder->addRowGroup([
                $builder->addRowInput('title', '菜单名称')->columnRatio(6)->desc('右侧菜单名称，尽可能5个字内'),
                $builder->addRowInput('short_title', '菜单短名称')->columnRatio(6)->desc('左侧菜单名称，尽可能5个字内(选填)'),
            ]);
            $builder->addRowGroup([
                $builder->addRowInput('plugin', '插件名称', 'xbCode')->desc('插件标识，默认插件标识为：xbCode'),
                $builder->addRowSelect('pid', '父级菜单')->type('tree-select')->desc('顶级菜单为一级菜单')->options(self::getCascaderOptions()),
            ]);
            $builder->addRowGroup([
                $builder->addRowInput('path', '菜单地址')->desc('普通菜单示例：admin/Index/index (对应：模块/控制器/方法)<br />'),
                $builder->addRowRadio('method', '请求类型', 'get')->options(MethodEnum::options())->desc('默认：GET请求类型'),
            ]);
            $builder->addRowGroup([
                $builder->addRowRadio('type', '菜单类型', '10')->options(MenuTypeEnum::options())->desc('请选择菜单类型')->columnRatio(3),
                $builder->addRowRadio('is_show', '是否显示', '10')->options(ShowEnum::options())->columnRatio(3)->desc('是否显示菜单图标'),
                $builder->addRowIconPicker('icon', '图标选择')->desc('菜单图标，显示在左侧菜单栏')->columnRatio(6),
            ]);
            $builder->addRowGroup([
                InputKV::make()->name('params')->label('附带参数')->keyPlaceholder('键名称')->valuePlaceholder('值参数'),
            ]);
        });
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
            ['is_saas', '=', '10'],
            ['type', '<>', '30']
        ];
        $data = AdminRule::where($where)->order('sort asc,id asc')->select()->toArray();
        $data = DataUtil::channelLevel($data, 0, '', 'id', 'pid');
        $data = MenuOption::getChildrenOptions($data);
        $data = array_merge([
            [
                'label' => '顶级菜单（顶级）',
                'value' => 0
            ]
        ], $data);
        return $data;
    }
}
