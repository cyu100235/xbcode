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
use plugin\xbAdmin\api\MenuChecked;
use plugin\xbAdmin\app\model\Admin;
use plugin\xbAdmin\enum\SideColorEnum;
use plugin\xbAdmin\app\model\AdminRule;
use plugin\xbAdmin\app\model\AdminRole;
use plugin\xbAdmin\enum\PrimaryColorEnum;
use plugin\xbCode\builder\Renders\XbForm;
use plugin\xbCode\builder\Renders\XbCrud;
use plugin\xbAdmin\app\validate\AdminRoleValidate;
use plugin\xbCode\builder\Components\Form\Transfer;
use plugin\xbAdmin\exception\business\ExceptionBusiness;

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
        $query = AdminRole::order('sort asc,id asc');
        $this->dataScope($query);
        if (($title = trim((string) $request->get('title', ''))) !== '') {
            $query->where('title', 'like', "%{$title}%");
        }
        $paginate = $query->paginate();
        $paginate->each(function ($item) {
            $item->num = Admin::where('role_id', (int) $item['id'])->count();
        });
        return $this->successData($paginate);
    }

    /**
     * 添加角色
     * @param Request $request
     * @return Response
     */
    public function add(Request $request)
    {
        if ($request->method() === 'POST') {
            $post = (array) $request->post();
            xbValidate(AdminRoleValidate::class, $post, 'add');
            $post['admin_id'] = $this->adminId();
            $post['is_system'] = YesEnum::NO['value'];
            // 新角色默认带上系统基础权限，避免建完角色就把管理员锁死
            $post['rule'] = AdminRole::stringifyRules($this->defaultRules());
            $model = new AdminRole;
            if (!$model->save($post)) {
                throw new ExceptionBusiness('保存失败');
            }
            return $this->success('保存成功');
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
        $model = $this->findModel((int) $request->get('id'));
        if ($request->method() === 'PUT') {
            $post = (array) $request->post();
            $this->guardSystem($model, '修改');
            xbValidate(AdminRoleValidate::class, $post, 'edit');
            unset($post['is_system'], $post['admin_id'], $post['rule']);
            if (!$model->save($post)) {
                throw new ExceptionBusiness('保存失败');
            }
            return $this->success('保存成功');
        }
        $builder = $this->formView();
        $builder->setSaveMethod('PUT');
        $builder->setData($model->toArray());
        return $this->successRes($builder);
    }

    /**
     * 删除角色
     * @param Request $request
     * @return Response
     */
    public function del(Request $request)
    {
        $model = $this->findModel((int) $request->get('id'));
        $this->guardSystem($model, '删除');
        $used = Admin::where('role_id', (int) $model['id'])->count();
        if ($used > 0) {
            throw new ExceptionBusiness('该角色下还有 ' . $used . ' 个管理员，请先转移后再删除');
        }
        if (!$model->delete()) {
            throw new ExceptionBusiness('删除失败');
        }
        return $this->success('删除成功');
    }

    /**
     * 分配权限
     * @param Request $request
     * @return Response
     */
    public function auth(Request $request)
    {
        $model = $this->findModel((int) $request->get('id'));
        if ($request->method() === 'PUT') {
            $this->guardSystem($model, '调整');
            $post = (array) $request->post();
            if (!array_key_exists('rules', $post)) {
                throw new ExceptionBusiness('未提交权限数据');
            }
            $rules = $this->normalizeRules($post['rules']);
            $this->guardGrantable($rules);
            if (!$model->save(['rule' => AdminRole::stringifyRules($rules)])) {
                throw new ExceptionBusiness('权限分配失败');
            }
            return $this->success('权限分配成功');
        }
        $grantable = $this->grantablePaths();
        $rules = AdminRule::order('sort asc,id asc')->select()->toArray();
        $rules = MenuChecked::menu2DToTree($rules);
        $rules = $this->getRules($rules);
        if ($grantable !== null) {
            $rules = $this->pruneRules($rules, $grantable);
        }
        $builder = XbForm::make();
        $builder->addRowStatic('title', '角色名称', (string) $model['title']);
        /** @var Transfer $transfer */
        $transfer = $builder->addRowTransfer('rules', '权限菜单', $this->valueRules($model));
        $transfer->selectTreeMode()
            ->selectTitle('权限列表')
            ->resultTitle('已选权限')
            ->searchable(true)
            ->columns([
                ['name' => 'label', 'label' => '权限名称'],
                ['name' => 'value', 'label' => '权限地址'],
            ])
            ->options($rules);
        $builder->setSaveMethod('PUT');
        return $this->successRes($builder);
    }

    /**
     * 部门主题设置
     *
     * 字段只对应 config/theme.php 顶层的可配置项，各布局的尺寸由配置文件统一维护；
     * 保存后由 PublicsController::layouts() 按当前管理员所属部门读取并覆盖默认布局。
     * @param Request $request
     * @throws ExceptionBusiness
     * @return Response
     */
    public function theme(Request $request)
    {
        $model = $this->findModel((int) $request->get('id'));
        if ($request->method() === 'PUT') {
            $theme = AdminRole::normalizeTheme((array) $request->post());
            if (!$model->save(['theme' => AdminRole::stringifyTheme($theme)])) {
                throw new ExceptionBusiness('保存失败');
            }
            return $this->success('保存成功');
        }
        $theme = array_merge(AdminRole::themeDefault(), AdminRole::normalizeTheme($model['theme'] ?? ''));
        $builder = XbForm::make();
        $builder->addRowStatic('title', '部门名称', (string) $model['title']);
        $builder->addRowGrid([
            $builder->addRowRadio('active', '布局模式', $theme['active'])
                ->options(LayoutEnum::options())
                ->inline(true)
                ->description('部门成员登录后台后使用的布局'),
            $builder->addRowRadio('mode', '主题模式', $theme['mode'])
                ->options(ThemeEnum::options())
                ->inline(true)
                ->description('部门成员登录后台后使用的主题模式'),
        ]);
        $builder->addRowCheckbox('layout', '可选布局模式', $theme['layout'])
            ->options(LayoutEnum::options())
            ->inline(true)
            ->description('勾选的布局会开放给部门成员在「主题设置」中切换，全部不选则关闭前端的主题设置');
        $builder->addRowSwitch('collapse', '默认折叠菜单', $theme['collapse']);
        $builder->addRowColor('colors', '主题颜色', $theme['colors'])
            ->description('留空使用内置主题色')
            ->presetColors(PrimaryColorEnum::getColumn('value'));
        $builder->addRowColor('sideColor', '菜单背景颜色', $theme['sideColor'])
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
     * @throws ExceptionBusiness
     * @return AdminRole
     */
    protected function findModel(int $id): AdminRole
    {
        if ($id <= 0) {
            throw new ExceptionBusiness('数据不存在');
        }
        $query = AdminRole::where('id', $id);
        $model = $this->dataScope($query)->find();
        if (!$model) {
            throw new ExceptionBusiness('该数据不存在');
        }
        return $model;
    }

    /**
     * 系统内置角色保护
     * @param AdminRole $model
     * @param string $action 操作描述
     * @throws ExceptionBusiness
     * @return void
     */
    protected function guardSystem(AdminRole $model, string $action): void
    {
        if ((string) $model['is_system'] === YesEnum::YES['value']) {
            throw new ExceptionBusiness('系统内置角色，禁止' . $action);
        }
    }

    /**
     * 已选权限的穿梭框取值
     *
     * 穿梭框以 extractValue=false 的整对象语义存储，这里补齐名称，
     * 右侧结果列表才能按 columns 正常展示。
     * @param AdminRole $model
     * @return array
     */
    protected function valueRules(AdminRole $model): array
    {
        $paths = AdminRole::parseRules($model['rule'] ?? '');
        if (!$paths) {
            return [];
        }
        $labels = AdminRule::column('title', 'path');
        $list = [];
        foreach ($paths as $path) {
            $list[] = [
                'label' => (string) ($labels[$path] ?? $path),
                'value' => (string) $path,
            ];
        }
        return $list;
    }

    /**
     * 权限树选项
     *
     * 叶子节点携带 path 作为可提交的值，父节点仅作为分组不参与取值。
     * @param array $data
     * @return array
     */
    protected function getRules(array $data): array
    {
        $list = [];
        foreach ($data as $value) {
            $children = $value['children'] ?? [];
            if (empty($children)) {
                $list[] = [
                    'label' => (string) ($value['title'] ?? ''),
                    'value' => (string) ($value['path'] ?? ''),
                ];
                continue;
            }
            $list[] = [
                'label' => (string) ($value['title'] ?? ''),
                'children' => $this->getRules($children),
            ];
        }
        return $list;
    }

    /**
     * 裁剪出当前账号可分配的权限树
     * @param array $rules
     * @param array $grantable
     * @return array
     */
    protected function pruneRules(array $rules, array $grantable): array
    {
        $list = [];
        foreach ($rules as $item) {
            if (isset($item['children'])) {
                $children = $this->pruneRules($item['children'], $grantable);
                if ($children) {
                    $list[] = ['label' => $item['label'], 'children' => $children];
                }
                continue;
            }
            if (in_array((string) $item['value'], $grantable, true)) {
                $list[] = $item;
            }
        }
        return $list;
    }

    /**
     * 归一化提交的权限值
     *
     * 兼容字符串数组、穿梭框的整对象数组、以及拼接后的逗号串三种形态。
     * @param mixed $rules
     * @return array
     */
    protected function normalizeRules(mixed $rules): array
    {
        if (is_string($rules)) {
            $rules = $rules === '' ? [] : explode(',', $rules);
        }
        if (!is_array($rules)) {
            return [];
        }
        $list = [];
        foreach ($rules as $item) {
            if (is_array($item)) {
                $item = $item['value'] ?? '';
            } elseif (is_object($item)) {
                $item = $item->value ?? '';
            }
            $item = trim((string) $item);
            if ($item !== '') {
                $list[] = $item;
            }
        }
        // 过滤掉已被删除的权限地址，避免残留脏数据
        $valid = AdminRule::column('path');
        $valid = array_map('strval', is_array($valid) ? $valid : []);
        return array_values(array_intersect($list, $valid));
    }

    /**
     * 当前账号可分配的权限地址
     * @return array|null null 表示不限制
     */
    protected function grantablePaths(): ?array
    {
        if ($this->isSystem()) {
            return null;
        }
        $model = AdminRole::where('id', (int) ($this->currentUser()['role_id'] ?? 0))->find();
        if (!$model) {
            return [];
        }
        return AdminRole::parseRules($model['rule'] ?? '');
    }

    /**
     * 校验待分配权限未越出自身权限范围
     * @param array $rules
     * @throws ExceptionBusiness
     * @return void
     */
    protected function guardGrantable(array $rules): void
    {
        $grantable = $this->grantablePaths();
        if ($grantable === null) {
            return;
        }
        if (array_diff($rules, $grantable)) {
            throw new ExceptionBusiness('存在超出自身权限范围的权限项，无法分配');
        }
    }

    /**
     * 新角色的默认权限（系统基础接口 + 当前账号可分配部分）
     * @return array
     */
    protected function defaultRules(): array
    {
        $paths = AdminRule::where('is_default', YesEnum::YES['value'])
            ->order('sort asc,id asc')
            ->column('path');
        $paths = array_map('strval', is_array($paths) ? $paths : []);
        $grantable = $this->grantablePaths();
        return $grantable === null ? $paths : array_values(array_intersect($paths, $grantable));
    }
}
