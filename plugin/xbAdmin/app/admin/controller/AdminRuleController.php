<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\app\admin\controller;

use support\Request;
use support\Response;
use plugin\xbAdmin\api\Url;
use plugin\xbAdmin\api\MenuOption;
use plugin\xbAdmin\enum\ShowEnum;
use plugin\xbAdmin\enum\StateEnum;
use plugin\xbAdmin\enum\YesEnum;
use plugin\xbAdmin\api\MenuChecked;
use plugin\xbAdmin\enum\MethodEnum;
use plugin\xbAdmin\enum\MenuTypeEnum;
use plugin\xbAdmin\app\model\AdminRule;
use plugin\xbCode\builder\Renders\XbForm;
use plugin\xbCode\builder\Renders\XbCrud;
use plugin\xbAdmin\app\validate\AdminRuleValidate;
use plugin\xbAdmin\exception\business\ExceptionBusiness;

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
            $query = AdminRule::order('sort asc,id asc');
            $title = trim((string) $request->get('title', ''));
            $path = trim((string) $request->get('path', ''));
            $type = (string) $request->get('type', '');
            $searching = $title !== '' || $path !== '';
            if ($title !== '') {
                $query->where('title', 'like', "%{$title}%");
            }
            if ($path !== '') {
                $query->where('path', 'like', "%{$path}%");
            }
            if (in_array($type, array_column(MenuTypeEnum::toArray(), 'value'), true)) {
                $query->where('type', $type);
                $searching = true;
            }
            $data = $query->select()->toArray();
            // 带检索条件时返回平铺结果，避免子节点因父级未命中而丢失
            if ($searching) {
                return $this->successData($data);
            }
            $data = MenuChecked::menu2DToTree($data);
            $data = MenuChecked::unsetMenusFields($data, ['_html', '_level']);
            return $this->successData($this->stripEmptyChildren($data));
        }
        $builder = XbCrud::make();
        $builder->useCRUD()->expandConfig([
            'expand' => 'accordion',
            'expandAll' => false,
        ]);
        $builder->addHeaderDialog('添加菜单', Url::make('AdminRule/add'), [
            'title' => '添加菜单',
            'size' => 'lg',
        ])->level('primary');
        $builder->addFilterInput('title', '菜单名称');
        $builder->addFilterInput('path', '路由地址');
        $builder->addFilterSelect('type', '菜单类型')->options(MenuTypeEnum::options())->clearable(true);
        $builder->addColumn('title', '菜单名称')->minWidth(160);
        $builder->addColumn('plugin', '插件标识')->width(120);
        $builder->addColumn('path', '路由地址')->minWidth(200);
        $builder->addColumnMap('type', '菜单类型', MenuTypeEnum::dict('label'))->width(100);
        $builder->addColumn('method', '请求类型')->width(100);
        $builder->addColumnIcon('icon', '菜单图标')->width(120);
        $builder->addColumnMap('state', '是否启用', StateEnum::dict('label'))->width(100);
        $builder->addColumnMap('is_show', '是否显示', ShowEnum::dict('label'))->width(100);
        $builder->addColumnMap('is_default', '默认菜单', YesEnum::dict('label'))->width(100);
        $builder->addColumnInput('sort', '菜单排序')->width(100);
        $builder->setActionConfig('width', 150);
        $builder->addRightActionDialog('修改', Url::make('edit'), [
            'title' => '修改菜单',
            'size' => 'lg',
        ])->disabledTip('系统菜单，禁止修改')
            ->disabledOn('this.is_system == 20');
        $builder->addRightActionConfirm('删除', Url::make('del'))
            ->disabledTip('系统菜单，禁止删除')
            ->disabledOn('this.is_system == 20');
        return $this->successRes($builder);
    }

    /**
     * 添加菜单
     * @param Request $request
     * @return Response
     */
    public function add(Request $request)
    {
        if ($request->method() === 'POST') {
            $post = (array) $request->post();
            xbAdminValidate(AdminRuleValidate::class, $post, 'add');
            $post = $this->prepareData($post);
            $post['is_system'] = YesEnum::NO['value'];
            $post['is_default'] = YesEnum::NO['value'];
            $model = new AdminRule;
            if (!$model->save($post)) {
                throw new ExceptionBusiness('添加菜单失败');
            }
            return $this->success('添加成功');
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
        $model = $this->findModel((int) $request->get('id'));
        if ($request->method() === 'PUT') {
            $this->guardSystem($model, '修改');
            $post = (array) $request->post();
            xbAdminValidate(AdminRuleValidate::class, $post, 'edit');
            $post = $this->prepareData($post);
            $this->guardPidLoop((int) $model['id'], (int) $post['pid']);
            unset($post['is_system'], $post['is_default']);
            if (!$model->save($post)) {
                throw new ExceptionBusiness('修改失败');
            }
            return $this->success('修改成功');
        }
        $data = $model->toArray();
        $data['params'] = json_decode((string) ($data['params'] ?? ''), true);
        $data['params'] = is_array($data['params']) ? $data['params'] : [];
        $builder = $this->formView();
        $builder->setSaveMethod('PUT');
        $builder->setData($data);
        return $this->successRes($builder);
    }

    /**
     * 列表快速编辑
     * @param Request $request
     * @return Response
     */
    public function rowEdit(Request $request)
    {
        $post = (array) $request->post();
        $model = $this->findModel((int) ($post['id'] ?? 0));
        $allow = ['sort', 'state'];
        if ((string) $model['is_system'] === YesEnum::YES['value']) {
            // 系统菜单只允许调整排序，停用会导致前端菜单缺失
            $allow = ['sort'];
        }
        $data = [];
        foreach ($allow as $field) {
            if (array_key_exists($field, $post)) {
                $data[$field] = $post[$field];
            }
        }
        if (empty($data)) {
            throw new ExceptionBusiness('没有需要保存的数据');
        }
        if (isset($data['sort'])) {
            $data['sort'] = (int) $data['sort'];
        }
        if (isset($data['state']) && !in_array((string) $data['state'], array_column(StateEnum::toArray(), 'value'), true)) {
            throw new ExceptionBusiness('启用状态值错误');
        }
        if (!$model->save($data)) {
            throw new ExceptionBusiness('修改失败');
        }
        return $this->success('修改成功');
    }

    /**
     * 删除菜单
     * @param Request $request
     * @return Response
     */
    public function del(Request $request)
    {
        $model = $this->findModel((int) $request->get('id'));
        $this->guardSystem($model, '删除');
        $children = AdminRule::where('pid', (int) $model['id'])->count();
        if ($children > 0) {
            throw new ExceptionBusiness('该菜单下还有 ' . $children . ' 个子项，请先删除子项');
        }
        if (!$model->delete()) {
            throw new ExceptionBusiness('删除失败');
        }
        return $this->success('删除成功');
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
            ->options(MenuOption::getCascaderOptions())
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
     * 整理提交的菜单数据
     * @param array $post
     * @return array
     */
    protected function prepareData(array $post): array
    {
        // tree-select 可能提交 {label,value} 结构
        if (isset($post['pid']) && is_array($post['pid'])) {
            $post['pid'] = $post['pid']['value'] ?? 0;
        }
        $post['pid'] = (int) ($post['pid'] ?? 0);
        if (isset($post['method']) && is_array($post['method'])) {
            $post['method'] = current($post['method']) ?: 'GET';
        }
        $post['method'] = strtoupper(trim((string) ($post['method'] ?? 'GET')));
        if (isset($post['is_show']) && is_array($post['is_show'])) {
            $post['is_show'] = current($post['is_show']);
        }
        if (isset($post['state']) && is_array($post['state'])) {
            $post['state'] = current($post['state']);
        }
        $params = $post['params'] ?? [];
        if (is_string($params)) {
            $params = json_decode($params, true);
        }
        $post['params'] = is_array($params) && $params
            ? (string) json_encode($params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            : '';
        $post['sort'] = (int) ($post['sort'] ?? 0);
        $post['short_title'] = trim((string) ($post['short_title'] ?? ''));
        $post['icon'] = trim((string) ($post['icon'] ?? ''));
        return $post;
    }

    /**
     * 剔除空的 children，避免前端渲染出无效展开箭头
     * @param array $data
     * @return array
     */
    protected function stripEmptyChildren(array $data): array
    {
        foreach ($data as &$value) {
            if (isset($value['children'])) {
                $value['children'] = $this->stripEmptyChildren((array) $value['children']);
                if (empty($value['children'])) {
                    unset($value['children']);
                }
            }
        }
        unset($value);
        return array_values($data);
    }

    /**
     * 校验父级层级，防止把自己挂到自己的子级下
     * @param int $id
     * @param int $pid
     * @throws ExceptionBusiness
     * @return void
     */
    protected function guardPidLoop(int $id, int $pid): void
    {
        if ($pid <= 0) {
            return;
        }
        if ($pid === $id) {
            throw new ExceptionBusiness('父级菜单不能是自己');
        }
        if (in_array($pid, $this->descendantIds($id, []), true)) {
            throw new ExceptionBusiness('父级菜单不能是自己的子级菜单');
        }
    }

    /**
     * 递归收集全部子孙菜单ID
     * @param int $id
     * @param array $visited 已访问节点，防存量脏数据造成死递归
     * @return array
     */
    protected function descendantIds(int $id, array $visited = []): array
    {
        $visited[$id] = true;
        $children = array_map('intval', AdminRule::where('pid', $id)->column('id') ?: []);
        $list = $children;
        foreach ($children as $child) {
            if (isset($visited[$child])) {
                continue;
            }
            $list = array_merge($list, $this->descendantIds($child, $visited));
        }
        return array_values(array_unique($list));
    }

    /**
     * 按ID查找菜单
     * @param int $id
     * @throws ExceptionBusiness
     * @return AdminRule
     */
    protected function findModel(int $id): AdminRule
    {
        if ($id <= 0) {
            throw new ExceptionBusiness('数据不存在');
        }
        $model = AdminRule::where('id', $id)->find();
        if (!$model) {
            throw new ExceptionBusiness('该数据不存在');
        }
        return $model;
    }

    /**
     * 系统菜单保护
     * @param AdminRule $model
     * @param string $action 操作描述
     * @throws ExceptionBusiness
     * @return void
     */
    protected function guardSystem(AdminRule $model, string $action): void
    {
        if ((string) $model['is_system'] === YesEnum::YES['value']) {
            throw new ExceptionBusiness('系统菜单，禁止' . $action);
        }
    }
}
