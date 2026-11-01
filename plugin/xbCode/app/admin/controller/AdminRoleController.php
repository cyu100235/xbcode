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

use support\Request;
use plugin\xbCode\api\Url;
use plugin\xbCode\api\Layouts;
use plugin\xbCode\app\model\Admin;
use plugin\xbCode\enum\SwitchEnum;
use plugin\xbCode\api\MenuChecked;
use plugin\xbCode\app\model\AdminRule;
use plugin\xbCode\app\model\AdminRole;
use plugin\xbCode\builder\Renders\XbForm;
use plugin\xbCode\builder\Renders\XbCrud;
use plugin\xbCode\exception\business\ExceptionBadRequest;
use plugin\xbCode\app\validate\AdminRoleValidate;

/**
 * 角色管理
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class AdminRoleController extends BaseController
{
    /**
     * 表格
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function index(Request $request)
    {
        $act = $request->get('_act');
        if ($act) {
            $builder = XbCrud::make();
            $builder->useCRUD()->alwaysShowPagination(true);
            // 表格列快速编辑接口
            $builder->useCRUD()->quickSaveItemApi(Url::make('rowEdit')->get());
            $builder->useCRUD()->quickSaveApi(Url::make('rowEdit')->get());
            $builder->setActionConfig('width', '200px');
            $builder->addHeaderDialog('添加角色', Url::make('AdminRole/add'), [
                'title' => '添加角色',
            ])->level('primary');

            $builder->addColumn('id', '序号')->width(80);
            $builder->addColumn('title', '角色名称');
            $builder->addColumn('num', '管理员人数');
            $builder->addColumnSwitch('is_single_login', '单点登录', SwitchEnum::switch())
                ->description('开启后同账号仅保留最新一次登录，其余会话自动下线');
            $builder->addColumnInput('sort', '排序');
            $builder->addColumn('create_at', '创建时间')->width(150);

            $builder->setActionConfig('width', 280);
            $builder->addRightActionDialog('主题设置', Url::make('theme'), [
                'title' => '「${title}」主题设置',
            ])->primary(true);
            $builder->addRightActionDialog('权限设置', Url::make('auth'), [
                'title' => '「${title}」权限设置',
            ])->success(true);
            $builder->addRightActionDialog('修改', Url::make('edit'), [
                'title' => '修改角色',
            ]);
            $builder->addRightActionConfirm('删除', Url::make('del'));
            return $this->successRes($builder);
        }
        $adminId = $request->uid;
        $data = AdminRole::where('admin_id', $adminId)
            ->order('sort asc,id asc')
            ->paginate()
            ->each(function ($item) {
                $where = [
                    'role_id' => $item['id'],
                ];
                $count = Admin::where($where)->count();
                $item->num = $count;
            });
        return $this->successRes($data);
    }

    /**
     * 添加
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function add(Request $request)
    {
        if ($request->method() == 'POST') {
            $adminId = $request->uid;
            $post = $request->post();

            // 数据验证
            xbValidate(AdminRoleValidate::class, $post, 'add');

            // 设置旗下角色
            $post['admin_id'] = $adminId;
            // 获取默认菜单
            $where = [
                'is_default' => '20',
            ];
            $rules = [];
            $list = AdminRule::where($where)->order('sort asc')->field('plugin,path')->select()->toArray();
            foreach ($list as $rule) {
                $rules[] = $this->getRuleValue($rule);
            }
            $post['rule'] = json_encode($rules, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            $model = new AdminRole;
            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            return $this->success('保存成功');
        }
        $builder = $this->formView();
        $builder->setSaveMethod('POST');
        return $this->successRes($builder);
    }

    /**
     * 修改
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function edit(Request $request)
    {
        $id = $request->get('id');
        $model = AdminRole::where('id', $id)->find();
        if (!$model) {
            return $this->fail('该数据不存在');
        }
        if ($request->method() == 'PUT') {
            $post = $request->post();

            // 数据验证
            xbValidate(AdminRoleValidate::class, $post, 'edit');

            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            return $this->success('保存成功');
        }
        $builder = $this->formView();
        $builder->setSaveMethod('PUT');
        $builder->setData($model);
        return $this->successRes($builder);
    }

    /**
     * 快速编辑（单点登录开关、排序）
     * @param Request $request
     * @throws ExceptionBadRequest
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function rowEdit(Request $request)
    {
        $id = (int) $request->post('id');
        $model = AdminRole::where('id', $id)->find();
        if (!$model) {
            return $this->fail('该数据不存在');
        }
        $post = (array) $request->post();
        $data = [];
        if (array_key_exists('is_single_login', $post)) {
            $value = (string) $post['is_single_login'];
            if (!in_array($value, SwitchEnum::getColumn('value'), true)) {
                throw new ExceptionBadRequest('单点登录参数错误');
            }
            $data['is_single_login'] = $value;
        }
        if (array_key_exists('sort', $post)) {
            $data['sort'] = (int) $post['sort'];
        }
        if (empty($data)) {
            throw new ExceptionBadRequest('没有需要保存的数据');
        }
        if (!$model->save($data)) {
            return $this->fail('修改失败');
        }
        return $this->success('修改成功');
    }

    /**
     * 删除
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function del(Request $request)
    {
        $id = $request->get('id');
        $model = AdminRole::where('id', $id)->find();
        if (!$model) {
            return $this->fail('该数据不存在');
        }
        // 检测是否系统管理员
        if ($model['is_system'] == '20') {
            return $this->fail('无法删除系统角色');
        }
        // 检测是否有管理员
        if (Admin::where('role_id', $id)->count()) {
            return $this->fail('该角色下存在管理员，无法删除');
        }
        // 删除管理员
        if (!$model->delete()) {
            return $this->fail('删除失败');
        }
        return $this->success('删除成功');
    }

    /**
     * 分配权限
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function auth(Request $request)
    {
        $id = $request->get('id');
        $model = AdminRole::where('id', $id)->find();
        if (!$model) {
            return $this->fail('该数据不存在');
        }
        if ($request->method() === 'PUT') {
            $rules = $request->post('rules');
            if ($rules && is_array($rules)) {
                $rules = array_values($rules);
            }
            if ($rules && is_string($rules)) {
                $rules = explode(',', $rules);
                $rules = array_filter($rules);
                $rules = array_values($rules);
            }
            $rules = $this->getRulePaths((array) $rules);
            // 默认权限不允许取消，保存时强制补齐
            $rules = array_values(array_unique(array_merge($rules, $this->getDefaultRulePaths())));
            if (empty($rules)) {
                return $this->fail('请选择权限规则');
            }
            if (!$model->save(['rule' => $rules])) {
                return $this->fail('权限分配失败');
            }
            return $this->success('权限分配成功');
        }
        // 解析已选择菜单规则
        $activeRules = $model['rule'];
        $activeRules = $this->getRulePaths($activeRules);
        $builder = XbForm::make();
        // 获取权限权限
        $rules = AdminRule::order('sort asc')->select()->toArray();
        $rules = MenuChecked::menu2DToTree($rules);
        // 统计重复的权限规则
        $duplicatePaths = $this->getDuplicateRulePaths($rules);
        $rules = $this->getRules($rules, $duplicatePaths);
        // 将已授权规则转换为树节点值
        $activeRules = $this->getRuleValues($rules, $activeRules);
        // 默认权限默认勾选
        $activeRules = array_values(array_unique(array_merge($activeRules, $this->getDefaultRuleValues($rules))));
        $builder->addRowGroup('role', [
            $builder->addRowInput('title', '角色名称', '', [
                'static' => true,
            ])->columnRatio(6),
            $builder->addRowInput('sort', '角色排序', '100')->columnRatio(6),
        ]);
        $builder->addRowInputTree('rules', '权限列表', $activeRules)
            ->multiple(true)
            ->cascade(true)
            ->showOutline(true)
            ->height('500px')
            ->options($rules);
        $builder->setSaveMethod('PUT');
        return $this->successRes($builder);
    }

    /**
     * 主题设置
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function theme(Request $request)
    {
        $id = $request->get('id');
        $model = AdminRole::where('id', $id)->find();
        if (!$model) {
            return $this->fail('该数据不存在');
        }
        if ($request->method() === 'PUT') {
            $post = $request->post();
            $theme = [
                'active' => $post['active'] ?? '',
                'mode' => $post['mode'] ?? '',
                'colors' => $post['colors'] ?? '',
                'sideColor' => $post['sideColor'] ?? '',
                'collapse' => !empty($post['collapse']),
            ];
            // theme 为 text 字段，需序列化后写入，否则 ORM 按 string 类型强转数组会报错
            $theme = json_encode($theme, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if (!$model->save(['theme' => $theme])) {
                return $this->fail('主题设置保存失败');
            }
            return $this->success('主题设置成功');
        }
        // 解析角色主题配置
        $theme = $model['theme'];
        if (is_string($theme)) {
            $theme = json_decode($theme, true) ?: [];
        }
        $theme = is_array($theme) ? $theme : [];
        // 合并默认布局
        $config = Layouts::config($theme);
        $builder = XbForm::make();
        $builder->addRowRadio('active', '布局模式', $config['active'])
            ->options(Layouts::layoutOptions())
            ->description('选择后台整体布局模式');
        $builder->addRowRadio('mode', '主题模式', $config['mode'])
            ->options(Layouts::modeOptions())
            ->description('选择后台主题显示模式');
        $builder->addRowGrid([
            $builder->addRowColor('colors', '主题颜色', $config['colors'])
                ->presetColors(Layouts::colorPresets())
                ->description('留空使用内置主题色'),
            $builder->addRowColor('sideColor', '左侧菜单颜色', $config['sideColor'])
                ->presetColors(Layouts::sideColorPresets())
                ->description('留空使用内置配色'),
        ]);
        $builder->addRowSwitch('collapse', '默认折叠菜单', $config['collapse'])
            ->description('开启后侧边菜单默认折叠');
        $builder->setSaveMethod('PUT');
        $builder->setData([
            'active' => $config['active'],
            'mode' => $config['mode'],
            'colors' => $config['colors'],
            'sideColor' => $config['sideColor'],
            'collapse' => $config['collapse'],
        ]);
        return $this->successRes($builder);
    }

    /**
     * 归一化权限规则为「插件标识/菜单地址」数组
     * 兼容前端提交的字符串、逗号串及 {label,value} 对象三种格式
     * @param array $rules
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function getRulePaths(array $rules): array
    {
        $list = [];
        foreach ($rules as $rule) {
            if (is_array($rule)) {
                $rule = $rule['value'] ?? '';
            }
            if (is_string($rule) && $rule !== '') {
                // 去掉重复规则节点的唯一标识后缀，还原为 插件标识/菜单地址
                $list[] = preg_replace('/#\d+$/', '', $rule);
            }
        }
        return array_values(array_unique($list));
    }

    /**
     * 拼接权限规则的唯一标识
     * @param array $rule 菜单数据
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function getRuleValue(array $rule): string
    {
        $path = $rule['path'] ?? '';
        if ($path === '') {
            return '';
        }
        $plugin = $rule['plugin'] ?? '';
        return $plugin !== '' ? "app/{$plugin}/{$path}" : $path;
    }

    /**
     * 统计权限树中重复的叶子规则
     * 不同插件的菜单可能使用相同地址（如 admin/Index/export），
     * 此时节点值必须唯一，否则勾选状态会回填到错误的节点上
     * @param array $data
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function getDuplicateRulePaths(array $data): array
    {
        $counts = [];
        $walk = function (array $nodes) use (&$walk, &$counts) {
            foreach ($nodes as $node) {
                if (!empty($node['children'])) {
                    $walk($node['children']);
                    continue;
                }
                $value = $this->getRuleValue($node);
                if ($value !== '') {
                    $counts[$value] = ($counts[$value] ?? 0) + 1;
                }
            }
        };
        $walk($data);
        $paths = [];
        foreach ($counts as $path => $count) {
            if ($count > 1) {
                $paths[] = $path;
            }
        }
        return $paths;
    }

    /**
     * 将已授权规则转换为权限树的节点值
     * 同一规则存在多个节点时，需要将全部节点勾选
     * @param array $rules 权限树
     * @param array $paths 已授权规则
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function getRuleValues(array $rules, array $paths): array
    {
        $values = [];
        $walk = function (array $nodes) use (&$walk, &$values, $paths) {
            foreach ($nodes as $node) {
                if (!empty($node['children'])) {
                    $walk($node['children']);
                    continue;
                }
                $value = $node['value'] ?? '';
                if ($value === '') {
                    continue;
                }
                // 节点值可能是 插件标识/菜单地址，也可能是 插件标识/菜单地址#ID
                $ruleValue = preg_replace('/#\d+$/', '', $value);
                $index = strpos($ruleValue, '/');
                // 兼容历史数据中仅存菜单地址的规则
                $rulePath = $index === false ? $ruleValue : substr($ruleValue, $index + 1);
                if (in_array($ruleValue, $paths, true) || in_array($rulePath, $paths, true)) {
                    $values[] = $value;
                }
            }
        };
        $walk($rules);
        return $values;
    }

    /**
     * 获取默认权限规则（is_default=20）
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function getDefaultRulePaths(): array
    {
        $rules = AdminRule::where('is_default', '20')
            ->order('sort asc')
            ->field('plugin,path')
            ->select()
            ->toArray();
        $paths = [];
        foreach ($rules as $rule) {
            $value = $this->getRuleValue($rule);
            if ($value !== '') {
                $paths[] = $value;
            }
        }
        return $paths;
    }

    /**
     * 将默认规则转换为权限树的节点值
     * @param array $rules 权限树
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function getDefaultRuleValues(array $rules): array
    {
        $values = [];
        $walk = function (array $nodes) use (&$walk, &$values) {
            foreach ($nodes as $node) {
                if (!empty($node['children'])) {
                    $walk($node['children']);
                    continue;
                }
                if (!empty($node['disabled']) && ($node['value'] ?? '') !== '') {
                    $values[] = $node['value'];
                }
            }
        };
        $walk($rules);
        return $values;
    }

    /**
     * 获取权限规则
     * @param array $data
     * @param array $duplicatePaths 重复的规则标识
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function getRules(array $data, array $duplicatePaths = [])
    {
        $list = [];
        $i = 0;
        foreach ($data as $value) {
            $list[$i]['label'] = $value['title'];
            // 判断是否为默认菜单规则
            $isDefault = ($value['is_default'] ?? '') === '20';
            if (empty($value['children'])) {
                // 节点值使用 插件标识/菜单地址，保证不同插件的相同地址不冲突
                $ruleValue = $this->getRuleValue($value);
                // 重复规则的节点追加ID后缀，保证节点值唯一
                $list[$i]['value'] = in_array($ruleValue, $duplicatePaths, true)
                    ? "{$ruleValue}#{$value['id']}"
                    : $ruleValue;
            } else {
                // 递归获取子节点
                $list[$i]['children'] = $this->getRules($value['children'], $duplicatePaths);
            }
            // 默认菜单规则默认勾选且不允许取消
            if ($isDefault) {
                $list[$i]['disabled'] = true;
            }
            $i++;
        }
        return $list;
    }

    /**
     * 表单视图
     * @return XbForm
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function formView()
    {
        $builder = XbForm::make();
        $builder->addRowInput('title', '角色名称');
        $builder->addRowInput('sort', '角色排序', '100');
        $builder->addRowSwitch('is_single_login', '单点登录', SwitchEnum::STATE10['value'], [
            'onText' => SwitchEnum::STATE20['label'],
            'offText' => SwitchEnum::STATE10['label'],
            'trueValue' => SwitchEnum::STATE20['value'],
            'falseValue' => SwitchEnum::STATE10['value'],
        ])->description('开启后同账号仅保留最新一次登录，其余会话自动下线');
        return $builder;
    }
}
