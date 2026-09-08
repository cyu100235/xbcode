<?php
/**
 * 插件数据导入 API
 *
 * 对应 PluginExport，反向把插件目录下的四份产物导入到本地系统：
 *   1. install.sql                 -> 数据库（表结构 DDL）
 *   2. config/menu.php             -> xb_admin_rule（菜单），按 plugin + path 幂等 upsert
 *   3. config/crontab.php          -> 预留（xbCrontab 启用后走 CrontabApi）
 *   4. config/dict.php             -> 预留（xbDict 启用后走字典安装流程）
 *
 * 与 Install 类的关系：
 *   Install 负责“安装周期”（建库 + 菜单 + 业务初始化），此处只做“数据导入”的原子操作，
 *   方便开发者对单个文件做增量导入（例如只导入菜单 / 只刷新表结构），不执行安装钩子。
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\api;

use Exception;
use plugin\xbAdmin\app\model\AdminRule;
use support\think\Db;

/**
 * 插件数据导入
 * 出现问题直接抛出异常，由外部命令捕获打印。
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class PluginImport
{
    /**
     * 一次性导入全部（SQL / 菜单 / 定时任务 / 字典）
     * @param string $name
     * @param string $menuPlugin
     * @return array{sql:int,menu:int,dict:?int,crontab:?int}
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function importAll(string $name, string $menuPlugin = ''): array
    {
        return [
            'sql'     => static::importSql($name),
            'menu'    => static::importMenus($name, $menuPlugin),
            'dict'    => static::importDict($name),
            'crontab' => static::importCrontab($name),
        ];
    }

    /**
     * 导入 install.sql
     *
     * 与导出对称：导出时把项目前缀替换回 `xb_` 模板前缀，导入时把 `xb_` 替换回
     * 当前环境配置前缀，再调用 Mysql::importSql 逐行执行 DDL。
     *
     * @param string $name 插件名
     * @return int 解析并执行的表数量
     * @throws Exception
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function importSql(string $name): int
    {
        $sqlPath = base_path() . "/plugin/{$name}/install.sql";
        if (!is_file($sqlPath)) {
            throw new Exception("install.sql 不存在: {$sqlPath}");
        }
        $sql = (string)file_get_contents($sqlPath);
        if ($sql === '') {
            throw new Exception("install.sql 内容为空");
        }
        $tableNames = Mysql::getSqlTableName($sql);
        if (empty($tableNames)) {
            throw new Exception("未从 install.sql 解析到表名，未导入任何表结构");
        }
        $tplPrefix = 'xb_';
        Mysql::importSql($sqlPath, $tplPrefix);
        return count($tableNames);
    }

    /**
     * 导入菜单（config/menu.php -> xb_admin_rule）
     *
     * 行为：
     *   - 树扁平化为节点，按 plugin + path 作为唯一键
     *   - 已存在：更新可改写字段（title/type/icon/params/is_show/is_default/is_system/state/sort/method）
     *   - 不存在：按 pid 顺序 insert，父节点 id 递归回填
     *   - 所有节点 plugin 字段统一使用 --menu-plugin（默认同插件名），避免菜单快照内写死 plugin 不匹配 --menu-plugin 场景
     *
     * @param string $name 目标插件名（读取其 config/menu.php）
     * @param string $menuPlugin 写入 xb_admin_rule.plugin 的值；空则用 $name
     * @return int 实际 upsert 节点数量（含新增和更新）
     * @throws Exception
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function importMenus(string $name, string $menuPlugin = ''): int
    {
        $menusPath = base_path() . "/plugin/{$name}/config/menu.php";
        if (!is_file($menusPath)) {
            throw new Exception("config/menu.php 不存在: {$menusPath}");
        }
        $tree = include $menusPath;
        if (!is_array($tree)) {
            throw new Exception("config/menu.php 返回值非数组: {$menusPath}");
        }
        if ($menuPlugin === '') {
            $menuPlugin = $name;
        }
        if (!class_exists(AdminRule::class)) {
            throw new Exception("缺少依赖：xbAdmin 插件未启用（找不到 model AdminRule，菜单无法写入 xb_admin_rule）");
        }
        $now = date('Y-m-d H:i:s');
        $count = 0;
        static::upsertMenuLevel($tree, 0, $menuPlugin, $now, $count);
        return $count;
    }

    /**
     * 导入 config/dict.php 字典（预留）
     * 当检测到 xbDict 插件启用时再扩展实现；否则返回 null 表示跳过
     * @param string $name
     * @return ?int
     * @throws Exception
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function importDict(string $name): ?int
    {
        $path = base_path() . "/plugin/{$name}/config/dict.php";
        if (!is_file($path)) {
            throw new Exception("config/dict.php 不存在: {$path}");
        }
        $dict = include $path;
        if (!is_array($dict)) {
            throw new Exception("config/dict.php 返回值非数组: {$path}");
        }
        if (empty($dict)) {
            return 0;
        }
        if (!class_exists('\plugin\xbDict\api\Install')) {
            return null; // 预留：待 xbDict 插件加载后实现
        }
        return null;
    }

    /**
     * 导入 config/crontab.php 定时任务（预留）
     * 当检测到 xbCrontab 插件启用时再扩展实现；否则返回 null 表示跳过
     * @param string $name
     * @return ?int
     * @throws Exception
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function importCrontab(string $name): ?int
    {
        $path = base_path() . "/plugin/{$name}/config/crontab.php";
        if (!is_file($path)) {
            throw new Exception("config/crontab.php 不存在: {$path}");
        }
        $crontabs = include $path;
        if (!is_array($crontabs)) {
            throw new Exception("config/crontab.php 返回值非数组: {$path}");
        }
        if (empty($crontabs)) {
            return 0;
        }
        if (!class_exists('\plugin\xbCrontab\api\CrontabApi')) {
            return null; // 预留：待 xbCrontab 插件加载后实现
        }
        return null;
    }

    /**
     * 递归 upsert 菜单层级，按顺序保证父 pid 先写，再下钻 children
     * @param array $level
     * @param int $pid
     * @param string $plugin
     * @param string $now
     * @param int $count
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private static function upsertMenuLevel(array $level, int $pid, string $plugin, string $now, int &$count): void
    {
        foreach ($level as $menu) {
            $path = (string)($menu['path'] ?? '');
            if ($path === '') {
                continue;
            }
            $data = [
                'title'       => (string)($menu['title'] ?? ''),
                'short_title' => (string)($menu['short_title'] ?? ''),
                'type'        => (string)($menu['type'] ?? '10'),
                'plugin'      => $plugin,
                'path'        => $path,
                'pid'         => $pid,
                'is_show'     => (string)($menu['is_show'] ?? '20'),
                'is_default'  => (string)($menu['is_default'] ?? '10'),
                'is_system'   => (string)($menu['is_system'] ?? '10'),
                'state'       => (string)($menu['state'] ?? '20'),
                'method'      => (string)($menu['method'] ?? ''),
                'icon'        => (string)($menu['icon'] ?? ''),
                'params'      => ($menu['params'] ?? ''),
                'sort'        => (int)($menu['sort'] ?? 100),
                'update_at'   => $now,
            ];
            $existing = Db::name('admin_rule')
                ->where('plugin', $plugin)
                ->where('path', $path)
                ->find();
            if ($existing) {
                Db::name('admin_rule')
                    ->where('id', (int)$existing['id'])
                    ->update($data);
                $newPid = (int)$existing['id'];
            } else {
                $data['create_at'] = $now;
                $newPid = (int)Db::name('admin_rule')->insertGetId($data);
            }
            $count++;
            $children = $menu['children'] ?? [];
            if (!empty($children) && is_array($children)) {
                static::upsertMenuLevel($children, $newPid, $plugin, $now, $count);
            }
        }
    }
}