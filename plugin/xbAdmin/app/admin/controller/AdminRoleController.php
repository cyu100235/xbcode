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
use plugin\xbAdmin\enum\YesEnum;
use plugin\xbAdmin\enum\ThemeEnum;
use plugin\xbAdmin\enum\LayoutEnum;
use plugin\xbCode\api\Gateway;
use plugin\xbAdmin\enum\SideColorEnum;
use plugin\xbAdmin\enum\PrimaryColorEnum;
use plugin\xbAdmin\app\BaseController;
use plugin\xbCode\builder\Renders\XbForm;
use plugin\xbCode\builder\Renders\XbCrud;
use plugin\xbCode\builder\Components\Form\Transfer;

/**
 * 角色管理
 */
class AdminRoleController extends BaseController
{
    /**
     * 角色列表
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        if ($request->get('_act')) {
            $builder = XbCrud::make();
            $builder->useCRUD()->alwaysShowPagination(true);
            $builder->addHeaderDialog('添加角色', Url::make('AdminRole/add'), [
                'title' => '添加角色',
                'size' => 'md',
            ])->level('primary');
            $builder->addFilterInput('title', '角色名称');
            $builder->addColumn('id', '序号')->width(80);
            $builder->addColumn('title', '角色名称')->minWidth(180);
            $builder->addColumn('num', '管理员人数')->width(110);
            $builder->addColumn('sort', '角色排序')->width(100);
            $builder->addColumnMap('is_system', '系统角色', YesEnum::dict('label'))->width(110);
            $builder->addColumnDateTime('create_at', '创建时间')->width(165);
            $builder->setActionConfig('width', 260);
            $builder->addRightActionDialog('主题设置', Url::make('theme'), [
                'title' => '主题设置',
            ])->success(true);
            $builder->addRightActionDialog('分配权限', Url::make('auth'), [
                'title' => '给「${title}」分配权限',
            ])->disabledTip('系统内置角色，权限固定不可调整')->disabledOn('this.is_system == 20');
            $builder->addRightActionDialog('修改', Url::make('edit'), [
                'title' => '修改角色',
            ])->disabledTip('系统内置角色，禁止修改')
                ->disabledOn('this.is_system == 20');
            $builder->addRightActionConfirm('删除', Url::make('del'))
                ->disabledTip('系统内置角色，禁止删除')
                ->disabledOn('this.is_system == 20');
            return $this->successRes($builder);
        }
        $result = Gateway::get('xbAdmin/api/AdminRole/index', request()->get());
        return $this->response($result);
    }

    /**
     * 添加角色
     * @param Request $request
     * @return Response
     */
    public function add(Request $request)
    {
        if ($request->method() === 'POST') {
            $result = Gateway::post('xbAdmin/api/AdminRole/add', (array) $request->post());
            return $this->response($result);
        }
        $builder = $this->formView();
        $builder->setSaveMethod('POST');
        return $this->successRes($builder);
    }

    /**
     * 修改角色
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request)
    {
        $id = (int) $request->get('id');
        if ($request->method() === 'PUT') {
            $result = Gateway::put("xbAdmin/api/AdminRole/edit?id={$id}", (array) $request->post());
            return $this->response($result);
        }
        $builder = $this->formView();
        $builder->setSaveMethod('PUT');
        $builder->setData($this->findModel($id));
        return $this->successRes($builder);
    }

    /**
     * 删除角色
     * @param Request $request
     * @return Response
     */
    public function del(Request $request)
    {
        $id = (int) $request->get('id');
        $result = Gateway::delete("xbAdmin/api/AdminRole/del?id={$id}");
        return $this->response($result);
    }

    /**
     * 分配权限
     * @param Request $request
     * @return Response
     */
    public function auth(Request $request)
    {
        $id = (int) $request->get('id');
        if ($request->method() === 'PUT') {
            $result = Gateway::put("xbAdmin/api/AdminRole/auth?id={$id}", (array) $request->post());
            return $this->response($result);
        }
        $data = Gateway::get("xbAdmin/api/AdminRole/auth?id={$id}")['data'] ?? [];
        $builder = XbForm::make();
        $builder->addRowStatic('title', '角色名称', (string) ($data['title'] ?? ''));
        /** @var Transfer $transfer */
        $transfer = $builder->addRowTransfer('rules', '权限菜单', $data['value'] ?? []);
        $transfer->selectTreeMode()
            ->selectTitle('权限列表')
            ->resultTitle('已选权限')
            ->searchable(true)
            ->columns([
                ['name' => 'label', 'label' => '权限名称'],
                ['name' => 'value', 'label' => '权限地址'],
            ])
            ->options($data['rules'] ?? []);
        $builder->setSaveMethod('PUT');
        return $this->successRes($builder);
    }

    /**
     * 部门主题设置
     *
     * 字段只对应 config/theme.php 顶层的可配置项，各布局的尺寸由配置文件统一维护；
     * 保存后由 PublicsController::layouts() 按当前管理员所属部门读取并覆盖默认布局。
     * @param Request $request
     * @return Response
     */
    public function theme(Request $request)
    {
        $id = (int) $request->get('id');
        if ($request->method() === 'PUT') {
            $result = Gateway::put("xbAdmin/api/AdminRole/theme?id={$id}", (array) $request->post());
            return $this->response($result);
        }
        $data = Gateway::get("xbAdmin/api/AdminRole/theme?id={$id}")['data'] ?? [];
        $theme = $data['theme'] ?? [];
        $builder = XbForm::make();
        $builder->addRowStatic('title', '部门名称', (string) ($data['title'] ?? ''));
        $builder->addRowGrid([
            $builder->addRowRadio('active', '布局模式', $theme['active'] ?? '')
                ->options(LayoutEnum::options())
                ->inline(true)
                ->description('部门成员登录后台后使用的布局'),
            $builder->addRowRadio('mode', '主题模式', $theme['mode'] ?? '')
                ->options(ThemeEnum::options())
                ->inline(true)
                ->description('部门成员登录后台后使用的主题模式'),
        ]);
        $builder->addRowCheckbox('layout', '可选布局模式', $theme['layout'] ?? [])
            ->options(LayoutEnum::options())
            ->inline(true)
            ->description('勾选的布局会开放给部门成员在「主题设置」中切换，全部不选则关闭前端的主题设置');
        $builder->addRowSwitch('collapse', '默认折叠菜单', $theme['collapse'] ?? false);
        $builder->addRowColor('colors', '主题颜色', $theme['colors'] ?? '')
            ->description('留空使用内置主题色')
            ->presetColors(PrimaryColorEnum::getColumn('value'));
        $builder->addRowColor('sideColor', '菜单背景颜色', $theme['sideColor'] ?? '')
            ->description('留空使用内置配色')
            ->presetColors(SideColorEnum::getColumn('value'));
        $builder->setSaveMethod('PUT');
        return $this->successRes($builder);
    }

    /**
     * 表单视图
     * @return XbForm
     */
    protected function formView(): XbForm
    {
        $builder = XbForm::make();
        $builder->addRowInput('title', '角色名称')->required(true)->description('2-50位，如：内容运营');
        $builder->addRowInputNumber('sort', '角色排序', 100)->description('数值越小越靠前');
        return $builder;
    }

    /**
     * 按数据范围查找角色
     * @param int $id
     * @return array
     */
    protected function findModel(int $id): array
    {
        $result = Gateway::get("xbAdmin/api/AdminRole/find?id={$id}");
        return $result['data'] ?? [];
    }
}