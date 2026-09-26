<?php
/**
 * 插件数据导出 API
 * 支持：表结构 -> install.sql、菜单 -> config/menu.php、定时任务 -> config/crontab.php（预留）、字典 -> config/dict.php（预留）
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\api;

use Brick\VarExporter\VarExporter;
use plugin\xbCode\api\Mysql;
use plugin\xbAdmin\app\model\AdminRule;

/**
 * 插件数据导出
 * 出现问题直接抛出异常，由外部捕获处理。
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class PluginExport
{
    /**
     * 导出全部数据
     * @param string $name 插件名
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function exportAll(string $name): array
    {
        return [
            'sql'     => static::exportSql($name),
            'menu'    => static::exportMenus($name),
            'dict'    => static::exportDict($name),
            'crontab' => static::exportCrontab($name),
        ];
    }

    /**
     * 导出表结构至 install.sql
     * 流程：解析 install.sql 提取表名 -> 替换为项目前缀 -> 查询数据库 -> 生成 DDL -> 写回文件
     * @param string $name 插件名
     * @return int 导出的表数量
     * @throws \Exception
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function exportSql(string $name): int
    {
        $sqlPath = base_path() . "/plugin/{$name}/install.sql";
        if (!is_file($sqlPath)) {
            throw new \Exception("install.sql 不存在: {$sqlPath}");
        }
        $sql = (string)file_get_contents($sqlPath);
        if ($sql === '') {
            throw new \Exception("install.sql 内容为空");
        }
        // 从 SQL 解析表名
        $tableNames = Mysql::getSqlTableName($sql);
        if (empty($tableNames)) {
            throw new \Exception("未从 install.sql 解析到表名");
        }

        // 获取项目表前缀
        $prefix       = Mysql::getConfig()['connections']['mysql']['prefix'] ?? 'xb_';
        $tplPrefix    = 'xb_';
        // 将模板表名替换为项目前缀表名（用于查询数据库）
        $tableNames   = array_map(function ($table) use ($prefix, $tplPrefix) {
            return str_replace($tplPrefix, $prefix, $table);
        }, $tableNames);

        $tableSqls = [];
        $count     = 0;
        foreach ($tableNames as $table) {
            // 检测表是否存在
            if (!Mysql::hasTable($table)) {
                continue;
            }
            // 模板表名（无项目前缀）
            $tplTable = str_replace($prefix, $tplPrefix, $table);
            // 获取表结构 SQL（不导出数据）
            $tableSql = Mysql::tablePreviewSql($table, false);
            // 将项目前缀替换为模板前缀
            $tableSql = str_replace("`{$prefix}", "`{$tplPrefix}", $tableSql);
            // 替换自增键始终为1
            if (str_contains($tableSql, 'AUTO_INCREMENT=')) {
                $tableSql = preg_replace('/AUTO_INCREMENT=\d+/', 'AUTO_INCREMENT=1', $tableSql);
            } else {
                $tableSql = str_replace('ENGINE=InnoDB', 'ENGINE=InnoDB AUTO_INCREMENT=1', $tableSql);
            }
            $tableSqls[] = "-- 删除表语句\nDROP TABLE IF EXISTS `{$tplTable}`;\n{$tableSql}";
            $count++;
        }

        if ($count === 0) {
            throw new \Exception("数据库中未找到任何匹配的表");
        }
        // tablePreviewSql 返回的每张表 SQL 已以换行结尾，这里再补一个换行即相邻表之间空一行
        file_put_contents($sqlPath, implode("\n", $tableSqls));
        return $count;
    }

    /**
     * 导出菜单至 config/menu.php
     * 流程：查询 xb_admin_rule -> 格式化字段 -> 转树形 -> 去除 id/pid -> VarExporter 格式化 -> 写入文件
     * @param string $name 插件名
     * @param string $menuPlugin 菜单归属插件名（数据库 plugin 字段，默认同 $name）
     * @return int 导出的顶级菜单数量
     * @throws \Exception
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function exportMenus(string $name, string $menuPlugin = ''): int
    {
        $menusPath = base_path() . "/plugin/{$name}/config/menu.php";
        if (!is_file($menusPath)) {
            throw new \Exception("config/menu.php 不存在: {$menusPath}");
        }

        if ($menuPlugin === '') { $menuPlugin = $name; }
        $menus = AdminRule::where('plugin', $menuPlugin)
            ->order('sort', 'asc')
            ->order('id', 'asc')
            ->select()
            ->toArray();
        if (empty($menus)) {
            throw new \Exception("未查询到菜单数据 (plugin={$menuPlugin})");
        }

        $menus = static::serializeMenus($menus);
        $menus = static::menu2DToTree($menus, 0);
        $menus = static::unsetMenusFields($menus, ['id', 'pid']);

        $content = VarExporter::export($menus);
        $export  = "<?php\n\nreturn " . $content . ";\n";
        file_put_contents($menusPath, $export);

        return count($menus);
    }

    /**
     * 导出字典至 config/dict.php（预留）
     * @param string $name 插件名
     * @return ?int 预留功能，未实现返回 null
     * @throws \Exception
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function exportDict(string $name): ?int
    {
        $path = base_path() . "/plugin/{$name}/config/dict.php";
        if (!is_file($path)) {
            throw new \Exception("config/dict.php 不存在: {$path}");
        }
        if (!class_exists('\plugin\xbDict\api\Install')) {
            return null;
        }
        return null;
    }

    /**
     * 导出定时任务至 config/crontab.php（预留）
     * @param string $name 插件名
     * @return ?int 预留功能，未实现返回 null
     * @throws \Exception
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function exportCrontab(string $name): ?int
    {
        $path = base_path() . "/plugin/{$name}/config/crontab.php";
        if (!is_file($path)) {
            throw new \Exception("config/crontab.php 不存在: {$path}");
        }
        if (!class_exists('\plugin\xbCrontab\api\CrontabApi')) {
            return null;
        }
        return null;
    }

    /**
     * 序列化菜单（格式化字段，统一类型）
     * @param array $menus 原始菜单数组
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private static function serializeMenus(array $menus): array
    {
        $result = [];
        foreach ($menus as $menu) {
            $item = [
                'title'      => (string)($menu['title'] ?? ''),
                'path'       => (string)($menu['path'] ?? ''),
                'method'     => self::methodToString($menu['method'] ?? 'GET'),
                'type'       => (string)($menu['type'] ?? '20'),
                'icon'       => (string)($menu['icon'] ?? ''),
                'params'     => $menu['params'] ?? '',
                'is_show'    => (string)($menu['is_show'] ?? '20'),
                'is_default' => (string)($menu['is_default'] ?? '10'),
                'is_system'  => (string)($menu['is_system'] ?? '10'),
                'state'      => (string)($menu['state'] ?? '20'),
                'sort'       => (int)($menu['sort'] ?? 0),
                'pid'        => (int)($menu['pid'] ?? 0),
                'id'         => (int)($menu['id'] ?? 0),
            ];
            $result[] = $item;
        }
        return $result;
    }

    /**
     * method 字段转字符串（模型访问器可能返回数组）
     * @param mixed $method
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private static function methodToString($method): string
    {
        if (is_array($method)) {
            $method = !empty($method) ? implode(',', $method) : '';
        }
        $method = (string)$method;
        return $method !== '' ? $method : 'GET';
    }

    /**
     * 扁平菜单转树形
     * @param array $menus 菜单数组
     * @param int $pid 父级 ID
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private static function menu2DToTree(array $menus, int $pid = 0): array
    {
        $tree = [];
        foreach ($menus as $menu) {
            if ((int)$menu['pid'] === $pid) {
                $menu['children'] = static::menu2DToTree($menus, (int)$menu['id']);
                $tree[] = $menu;
            }
        }
        return $tree;
    }

    /**
     * 递归去除菜单字段
     * @param array $menus 菜单树
     * @param array $fields 待移除字段名
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private static function unsetMenusFields(array $menus, array $fields): array
    {
        foreach ($menus as $i => $menu) {
            foreach ($fields as $field) {
                unset($menus[$i][$field]);
            }
            if (!empty($menu['children'])) {
                $menus[$i]['children'] = static::unsetMenusFields($menu['children'], $fields);
            }
        }
        return $menus;
    }
}