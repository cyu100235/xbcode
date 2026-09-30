<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbAdmin\app\api\controller;

use support\Request;
use support\Response;
use plugin\xbAdmin\enum\YesEnum;
use plugin\xbAdmin\api\MenuChecked;
use plugin\xbAdmin\app\model\Admin;
use plugin\xbAdmin\app\model\AdminRule;
use plugin\xbAdmin\app\model\AdminRole;
use plugin\xbAdmin\app\BaseController;
use plugin\xbAdmin\app\validate\AdminRoleValidate;
use plugin\xbAdmin\exception\business\ExceptionBusiness;

/**
 * 角色管理接口
 * @copyright 贵州云铺网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
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

    /**
     * 修改角色
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request)
    {
        $model = $this->findModel((int) $request->get('id'));
        $this->guardSystem($model, '修改');
        $post = (array) $request->post();
        xbValidate(AdminRoleValidate::class, $post, 'edit');
        unset($post['is_system'], $post['admin_id'], $post['rule']);
        if (!$model->save($post)) {
            throw new ExceptionBusiness('保存失败');
        }
        return $this->success('保存成功');
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
     * 查询数据
     * @param Request $request
     * @return Response
     */
    public function find(Request $request)
    {
        $model = $this->findModel((int) $request->get('id'));
        return $this->successRes($model);
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
        return $this->successRes([
            'title' => (string) $model['title'],
            'rules' => $rules,
            'value' => $this->valueRules($model),
        ]);
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
        $model = $this->findModel((int) $request->get('id'));
        if ($request->method() === 'PUT') {
            $theme = AdminRole::normalizeTheme((array) $request->post());
            if (!$model->save(['theme' => AdminRole::stringifyTheme($theme)])) {
                throw new ExceptionBusiness('保存失败');
            }
            return $this->success('保存成功');
        }
        $theme = array_merge(AdminRole::themeDefault(), AdminRole::normalizeTheme($model['theme'] ?? ''));
        return $this->successRes([
            'title' => (string) $model['title'],
            'theme' => $theme,
        ]);
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