<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\app\admin\controller;

use Exception;
use plugin\xbCode\api\Url;
use plugin\xbCode\enum\ShowEnum;
use plugin\xbCode\enum\StateEnum;
use plugin\xbCode\utils\DataUtil;
use plugin\xbCode\api\MenuOption;
use plugin\xbCode\enum\MethodEnum;
use plugin\xbCode\api\MenuChecked;
use plugin\xbCode\enum\MenuTypeEnum;
use plugin\xbCode\app\model\AdminRule;
use plugin\xbCode\builder\Renders\XbForm;
use plugin\xbCode\builder\Renders\XbCrud;
use plugin\xbCode\app\validate\AdminRuleValidate;

/**
 * 菜单管理
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class AdminRuleController extends BaseController
{
    /**
     * 表格
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function index()
    {
        $act = (int) request()->get('_act');
        if ($act) {
            $where = [];
            $data = AdminRule::where($where)->order('sort asc,id asc')->select()->toArray();
            $data = MenuChecked::menu2DToTree($data);
            $data = MenuChecked::unsetMenusFields($data, [
                '_html',
                '_level',
            ]);
            return $this->successData($data);
        }
        $builder = XbCrud::make(function (XbCrud $builder) {
            $builder->useCRUD()->expandConfig([
                'expand' => 'accordion',
                'expandAll' => false,
            ]);
            $builder->setActionConfig('width', 150);
            $builder->addHeaderDialog('添加菜单', Url::make('add'), [
                'title' => '添加菜单',
            ])
                ->level('primary');
            $builder->addRightActionDialog('修改', Url::make('edit'), [
                'title' => '修改菜单',
            ])
                ->disabledTip('系统菜单，禁止修改')
                ->disabledOn('this.is_system == 20');
            $builder->addRightActionConfirm('删除', Url::make('del'))
                ->disabledTip('系统菜单，禁止删除')
                ->disabledOn('this.is_system == 20')
                ->style(['color' => 'red']);

            // 设置表格列快速编辑
            $builder->useCRUD()->quickSaveItemApi(Url::make('AdminRule/rowEdit'));
            $builder->useCRUD()->quickSaveApi(Url::make('rowEdit'));
            // 添加表格列
            $builder->addColumn('title', '菜单名称');
            $builder->addColumn('plugin', '插件标识');
            $builder->addColumn('path', '路由地址');
            $builder->addColumnMap('type', '菜单类型', MenuTypeEnum::dict())->width(100);
            $builder->addColumn('method', '请求类型');
            $builder->addColumnIcon('icon', '菜单图标');
            $builder->addColumnMap('state', '是否启用', StateEnum::dict())->width(100);
            $builder->addColumnMap('is_show', '是否显示', ShowEnum::dict())->width(100);
            $builder->addColumnInput('sort', '菜单排序')->width(100);
        });
        return $this->successRes($builder);
    }

    /**
     * 快速编辑
     * @throws Exception
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function rowEdit()
    {
        $id = (int) request()->post('id');
        $post = request()->post();
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
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function add()
    {
        if (request()->method() === 'POST') {
            // 获取数据
            $post = request()->post();
            // 数据验证
            xbValidate(AdminRuleValidate::class, $post, 'add');
            // 设置父级菜单
            $post['pid'] = isset($post['pid']['value']) ? $post['pid']['value'] : $post['pid'];
            // 附带参数
            $post['params'] = json_encode($post['params'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            // 保存数据
            $model = new AdminRule;
            if (!$model->save($post)) {
                throw new Exception('添加菜单失败');
            }
            // 返回结果
            return $this->success('添加成功');
        }
        $builder = $this->formView();
        $builder->setSaveMethod('POST');
        return $this->successRes($builder);
    }

    /**
     * 修改
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function edit()
    {
        $id = (int) request()->get('id');
        $model = AdminRule::find($id);
        if (!$model) {
            return $this->fail('数据不存在');
        }
        if (request()->method() === 'PUT') {
            // 获取数据
            $post = request()->post();
            // 数据验证
            xbValidate(AdminRuleValidate::class, $post, 'edit');
            // 设置父级菜单
            $post['pid'] = isset($post['pid']['value']) ? $post['pid']['value'] : $post['pid'];
            // 附带参数
            $post['params'] = json_encode($post['params'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
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
        $data['params'] = json_decode($data['params'], true);
        // 设置表单
        $builder = $this->formView();
        $builder->setSaveMethod('PUT');
        $builder->setData($data);
        return $this->successRes($builder);
    }

    /**
     * 删除
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function del()
    {
        $id = (int) request()->get('id');
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
     * @return XbForm
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function formView()
    {
        $builder = XbForm::make(function (XbForm $builder) {
            $builder->useForm()->columnCount(2);
            $builder->addRowInput('title', '菜单名称')
                ->required(true)
                ->description('右侧菜单名称，尽可能5个字内');
            $builder->addRowInput('short_title', '菜单短名称')
                ->description('左侧菜单名称，尽可能5个字内(选填)');
            $builder->addRowInput('plugin', '插件名称', 'xbCode')
                ->required(true)
                ->description('插件标识，默认插件标识为：xbCode');
            $builder->addRowSelect('pid', '父级菜单')
                ->type('tree-select')
                ->required(true)
                ->description('顶级菜单为一级菜单')
                ->options(self::getCascaderOptions());
            $builder->addRowInput('path', '菜单地址')
                ->required(true)
                ->description('普通菜单示例：admin/Index/index (对应：模块/控制器/方法)<br />');
            $builder->addRowRadioButton('method', '请求类型', 'GET')
                ->required(true)
                ->options(MethodEnum::options())
                ->description('默认：GET请求类型');
            $builder->addRowRadioButton('type', '菜单类型', '10')
                ->required(true)
                ->options(MenuTypeEnum::options())
                ->description('请选择菜单类型');
            $builder->addRowRadioButton('is_show', '是否显示', '10')
                ->required(true)
                ->options(ShowEnum::options())
                ->description('是否显示菜单图标');
            $builder->addRowIconPicker('icon', '图标选择')
                ->description('菜单图标，显示在左侧菜单栏');
            $builder->addRowKeyValue('params', '附带参数')
                ->keyPlaceholder('键名称')
                ->valuePlaceholder('值参数');
        });
        return $builder;
    }

    /**
     * 获取多级选项
     * @return array<int|string>[]
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function getCascaderOptions()
    {
        $where = [
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
