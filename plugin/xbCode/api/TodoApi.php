<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\api;

use support\Log;

/**
 * 后台待办事项
 *
 * 待办数据由各插件自行提供：插件在 config/xbcode.php 中声明 `todo`，
 * 取值为条目数组、闭包或「类::方法」可调用数组，返回结构如下：
 * [
 *     'title' => '待审核提现',       // 必填，待办名称
 *     'desc'  => '有 3 笔待审核',    // 可选，补充说明
 *     'count' => 3,                  // 必填，待办数量，为 0 时不展示
 *     'icon'  => 'AuditOutlined',    // 可选，图标名称
 *     'path'  => '/app/xbWithdraw/admin/Withdraw/index', // 必填，点击跳转的页面地址
 *     'query' => ['status' => '10'], // 可选，跳转携带参数
 *     'sort'  => 1,                  // 可选，排序值，越小越靠前
 *     'urgent' => true,              // 可选，紧急事项会弹窗提醒
 * ]
 */
class TodoApi
{
    /**
     * 汇总全部插件的待办事项
     * @param int $uid 管理员ID，传入后按该管理员的菜单权限过滤
     * @return array{enabled:bool,count:int,list:array}
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function summary(int $uid = 0): array
    {
        $permissions = static::permissions($uid);
        $list = [];
        // 是否至少有一条待办对自己可见：无权限访问任何待办页面时前端不展示铃铛、不轮询
        $enabled = false;
        foreach (static::providers() as $plugin => $provider) {
            foreach (static::items($provider) as $item) {
                $path = trim((string) ($item['path'] ?? ''));
                // 无权限访问的页面不展示待办，也不计入可见性
                if ($path === '' || ($permissions !== null && !static::hasPermission($path, $plugin, $permissions))) {
                    continue;
                }
                $enabled = true;
                $row = static::normalize($item, $plugin);
                if ($row !== null) {
                    $list[] = $row;
                }
            }
        }
        // 先按声明排序值、再按待办数量倒序
        usort($list, function ($a, $b) {
            if ($a['sort'] !== $b['sort']) {
                return $a['sort'] <=> $b['sort'];
            }
            return $b['count'] <=> $a['count'];
        });
        return [
            'enabled' => $enabled,
            'count' => array_sum(array_column($list, 'count')),
            'list' => $list,
        ];
    }

    /**
     * 获取已启用插件声明的待办数据源
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function providers(): array
    {
        $files = glob(base_path() . '/plugin/*/config/xbcode.php') ?: [];
        $pluginsApi = PluginsApi::make();
        $data = [];
        foreach ($files as $path) {
            // 获取插件标识
            $plugin = basename(dirname($path, 2));
            // 仅加载已启用插件的待办
            if (!$pluginsApi->hasEnabled($plugin)) {
                continue;
            }
            $content = file_get_contents($path);
            if (empty($content)) {
                continue;
            }
            $config = include $path;
            $todo = $config['todo'] ?? null;
            if (empty($todo)) {
                continue;
            }
            $data[$plugin] = $todo;
        }
        return $data;
    }

    /**
     * 解析单个插件提供的待办条目
     * @param mixed $provider 待办数据源
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function items(mixed $provider): array
    {
        try {
            $items = is_callable($provider) ? $provider() : $provider;
        } catch (\Throwable $th) {
            if (DebugApi::status()) {
                Log::info('待办事项获取失败：' . $th->getMessage());
            }
            return [];
        }
        return is_array($items) ? $items : [];
    }

    /**
     * 格式化待办条目（权限已在外层过滤）
     * @param array $item 待办条目
     * @param string $plugin 插件标识
     * @return array|null
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function normalize(array $item, string $plugin): ?array
    {
        $title = trim((string) ($item['title'] ?? ''));
        $path = trim((string) ($item['path'] ?? ''));
        $count = (int) ($item['count'] ?? 0);
        // 标题、地址缺失或数量为 0 的待办不展示
        if ($title === '' || $path === '' || $count <= 0) {
            return null;
        }
        return [
            'title' => $title,
            'desc' => (string) ($item['desc'] ?? ''),
            'count' => $count,
            'icon' => (string) ($item['icon'] ?? ''),
            'path' => $path,
            'query' => is_array($item['query'] ?? null) ? $item['query'] : [],
            'sort' => (int) ($item['sort'] ?? 0),
            // 紧急事项会在登录后台后弹窗提醒
            'urgent' => !empty($item['urgent']),
            'plugin' => $plugin,
        ];
    }

    /**
     * 判断管理员是否有待办页面的访问权限
     * @param string $path 待办页面地址
     * @param string $plugin 插件标识
     * @param array $permissions 管理员权限路径
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function hasPermission(string $path, string $plugin, array $permissions): bool
    {
        $path = trim($path, '/');
        // 权限规则格式为「插件标识/菜单地址」，兼容仅存菜单地址的历史数据
        $candidates = array_filter([
            $path,
            str_starts_with($path, 'app/') ? $path : "app/{$plugin}/{$path}",
        ]);
        foreach ($permissions as $permission) {
            if (in_array(trim($permission, '/'), $candidates, true)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 获取当前管理员的菜单权限路径
     * @param int $uid 管理员ID
     * @return array|null null 表示系统角色或未限定，不做过滤
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function permissions(int $uid): ?array
    {
        if (empty($uid)) {
            return null;
        }
        try {
            $user = AdminApi::userinfo($uid);
        } catch (\Throwable $th) {
            return null;
        }
        // 系统管理员不校验权限
        if ((string) ($user['role']['is_system'] ?? '') === '20') {
            return null;
        }
        $permissions = AdminApi::getRulePaths($user['permission'] ?? []);
        // 与前端一致：未配置权限规则时不作限制
        return empty($permissions) ? null : $permissions;
    }
}
