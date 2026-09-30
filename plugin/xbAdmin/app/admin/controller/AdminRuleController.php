<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbAdmin\app\admin\controller;

use support\Request;
use support\Response;
use plugin\xbAdmin\api\Url;
use plugin\xbAdmin\enum\ShowEnum;
use plugin\xbAdmin\enum\StateEnum;
use plugin\xbAdmin\enum\YesEnum;
use plugin\xbCode\api\Gateway;
use plugin\xbAdmin\enum\MethodEnum;
use plugin\xbAdmin\enum\MenuTypeEnum;
use plugin\xbAdmin\app\BaseController;
use plugin\xbCode\builder\Renders\XbForm;
use plugin\xbCode\builder\Renders\XbCrud;

/**
 * 菜单（权限规则）管理
 */
class AdminRuleController extends BaseController
{
    /**
     * 菜单列表
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        if ($request->get('_act')) {
            $builder = XbCrud::make();
            $builder->useCRUD()->quickSaveItemApi(Url::make('rowEdit'));
            $builder->useCRUD()->expandConfig([
                'expand' => 'accordion',
                'expandAll' => false,
            ]);
            $builder->addHeaderDialog('添加菜单', Url::make('AdminRule/add'))
                ->primary()->title('添加菜单')->size('lg');
            $builder->addFilterInput('title', '菜单名称');
            $builder->addFilterInput('path', '路由地址');
            $builder->addFilterSelect('type', '菜单类型')->options(MenuTypeEnum::options())->clearable(true);
            $builder->addColumn('title', '菜单名称')->width(160);
            $builder->addColumn('plugin', '插件标识')->width(120);
            $builder->addColumn('path', '路由地址')->width(200);
            $builder->addColumnMap('type', '菜单类型', MenuTypeEnum::dict())->width(100);
            $builder->addColumn('method', '请求类型')->width(100);
            $builder->addColumnIcon('icon', '菜单图标')->width(120);
            $builder->addColumnMap('state', '是否启用', StateEnum::dict())->width(100);
            $builder->addColumnMap('is_show', '是否显示', ShowEnum::dict())->width(100);
            $builder->addColumnMap('is_default', '默认菜单', YesEnum::dict())->width(100);
            $builder->addColumnInput('sort', '菜单排序')->width(100);
            $builder->setActionConfig('width', 150);
            $builder->addRightActionDialog('修改', Url::make('edit'), [
                'title' => '修改菜单',
            ])->disabledTip('系统菜单，禁止修改')
                ->disabledOn('this.is_system == 20');
            $builder->addRightActionConfirm('删除', Url::make('del'))
                ->disabledTip('系统菜单，禁止删除')
                ->disabledOn('this.is_system == 20');
            return $this->successRes($builder);
        }
        $result = Gateway::get('xbAdmin/api/AdminRule/index', request()->get());
        return $this->response($result);
    }

    /**
     * 添加菜单
     * @param Request $request
     * @return Response
     */
    public function add(Request $request)
    {
        if ($request->method() === 'POST') {
            $result = Gateway::post('xbAdmin/api/AdminRule/add', (array) $request->post());
            return $this->response($result);
        }
        $builder = $this->formView();
        $builder->setSaveMethod('POST');
        return $this->successRes($builder);
    }

    /**
     * 修改菜单
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request)
    {
        $id = (int) $request->get('id');
        if ($request->method() === 'PUT') {
            $result = Gateway::put("xbAdmin/api/AdminRule/edit?id={$id}", (array) $request->post());
            return $this->response($result);
        }
        $builder = $this->formView();
        $builder->setSaveMethod('PUT');
        $builder->setData($this->findModel($id));
        return $this->successRes($builder);
    }

    /**
     * 列表快速编辑
     * @param Request $request
     * @return Response
     */
    public function rowEdit(Request $request)
    {
        $result = Gateway::put('xbAdmin/api/AdminRule/rowEdit', (array) $request->post());
        return $this->response($result);
    }

    /**
     * 删除菜单
     * @param Request $request
     * @return Response
     */
    public function del(Request $request)
    {
        $id = (int) $request->get('id');
        $result = Gateway::delete("xbAdmin/api/AdminRule/del?id={$id}");
        return $this->response($result);
    }

    /**
     * 表单视图
     * @return XbForm
     */
    protected function formView(): XbForm
    {
        $builder = XbForm::make();
        $builder->useForm()->columnCount(2);
        $builder->addRowInput('title', '菜单名称')
            ->required(true)
            ->description('右侧菜单名称，建议5个字以内');
        $builder->addRowInput('short_title', '菜单短名称')
            ->description('左侧折叠菜单名称，可留空');
        $builder->addRowSelect('pid', '父级菜单')
            ->type('tree-select')
            ->required(true)
            ->options($this->menuOptions())
            ->description('不选父级即为顶级菜单');
        $builder->addRowInput('plugin', '插件标识', 'xbAdmin')
            ->required(true)
            ->description('菜单归属插件，本插件固定为：xbAdmin');
        $builder->addRowInput('path', '路由地址')
            ->required(true)
            ->description('普通菜单：admin/Admin/index；按钮：与所属菜单同一路由地址即可');
        $builder->addRowRadioButton('type', '菜单类型', '20')
            ->required(true)
            ->options(MenuTypeEnum::options())
            ->description('目录仅分组，菜单对应页面，按钮用于接口鉴权');
        $builder->addRowRadioButton('method', '请求类型', 'GET')
            ->required(true)
            ->options(MethodEnum::options())
            ->description('与接口实际请求方式保持一致');
        $builder->addRowRadioButton('is_show', '是否显示', '20')
            ->required(true)
            ->options(ShowEnum::options())
            ->description('隐藏后仍可通过地址访问');
        $builder->addRowRadioButton('state', '是否启用', StateEnum::ENABLED['value'])
            ->options(StateEnum::options())
            ->required(true)
            ->description('停用后不再下发给前端');
        $builder->addRowIconPicker('icon', '菜单图标')
            ->description('显示在左侧菜单栏的图标');
        $builder->addRowKeyValue('params', '附带参数')
            ->keyPlaceholder('键名称')
            ->valuePlaceholder('值参数');
        return $builder;
    }

    /**
     * 父级菜单选项
     * @return array
     */
    protected function menuOptions(): array
    {
        return Gateway::get('xbAdmin/api/AdminRule/options')['data'] ?? [];
    }

    /**
     * 按ID查找菜单
     * @param int $id
     * @return array
     */
    protected function findModel(int $id): array
    {
        $result = Gateway::get("xbAdmin/api/AdminRule/find?id={$id}");
        return $result['data'] ?? [];
    }
}