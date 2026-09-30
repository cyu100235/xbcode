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
use plugin\xbAdmin\api\MenuOption;
use plugin\xbAdmin\enum\StateEnum;
use plugin\xbAdmin\api\MenuChecked;
use plugin\xbAdmin\enum\MenuTypeEnum;
use plugin\xbAdmin\app\model\AdminRule;
use plugin\xbAdmin\app\BaseController;
use plugin\xbAdmin\app\validate\AdminRuleValidate;
use plugin\xbAdmin\exception\business\ExceptionBusiness;

/**
 * 菜单（权限规则）接口
 * @copyright 贵州云铺网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
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

    /**
     * 添加菜单
     * @param Request $request
     * @return Response
     */
    public function add(Request $request)
    {
        $post = (array) $request->post();
        xbValidate(AdminRuleValidate::class, $post, 'add');
        $post = $this->prepareData($post);
        // 同一插件 + 同一路由视为同一条菜单，已存在则更新（插件安装可反复执行）
        $model = AdminRule::where([
            'plugin' => (string) ($post['plugin'] ?? ''),
            'path' => $post['path'],
        ])->find();
        if ($model) {
            $this->guardPidLoop((int) $model['id'], (int) $post['pid']);
        } else {
            $model = new AdminRule;
        }
        // 插件 config/menu.php 会声明 is_system / is_default，后台表单不提交时按普通菜单处理
        foreach (['is_system', 'is_default'] as $field) {
            $value = (string) ($post[$field] ?? '');
            $post[$field] = in_array($value, [YesEnum::NO['value'], YesEnum::YES['value']], true)
                ? $value
                : YesEnum::NO['value'];
        }
        if (!$model->save($post)) {
            throw new ExceptionBusiness('添加菜单失败');
        }
        // 返回菜单ID，供插件安装时回填子级 pid
        return $this->success('添加成功', ['id' => (int) $model['id']]);
    }

    /**
     * 修改菜单
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request)
    {
        $model = $this->findModel((int) $request->get('id'));
        $this->guardSystem($model, '修改');
        $post = (array) $request->post();
        xbValidate(AdminRuleValidate::class, $post, 'edit');
        $post = $this->prepareData($post);
        $this->guardPidLoop((int) $model['id'], (int) $post['pid']);
        unset($post['is_system'], $post['is_default']);
        if (!$model->save($post)) {
            throw new ExceptionBusiness('修改失败');
        }
        return $this->success('修改成功');
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
     * 查询数据
     * @param Request $request
     * @return Response
     */
    public function find(Request $request)
    {
        $model = $this->findModel((int) $request->get('id'));
        $data = $model->toArray();
        $data['params'] = json_decode((string) ($data['params'] ?? ''), true);
        $data['params'] = is_array($data['params']) ? $data['params'] : [];
        return $this->successRes($data);
    }

    /**
     * 父级菜单选项
     * @return Response
     */
    public function options()
    {
        return $this->successRes(MenuOption::getCascaderOptions());
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