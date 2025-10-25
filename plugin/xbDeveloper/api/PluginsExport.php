<?php
namespace plugin\xbDeveloper\api;

use Exception;
use think\facade\Db;
use plugin\xbCode\api\MenuChecked;
use plugin\xbCode\app\model\AdminRule;
use plugin\xbCode\utils\MysqlUtil;

/**
 * 插件导出
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class PluginsExport
{
    /**
     * 导出全部数据
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function exportAllData(string $name)
    {
        // 导出表结构
        static::exportSql($name);
        // 导出菜单
        static::exportMenus($name);
        // 导出字典
        static::exportDict($name);
        // 导出定时任务
        static::exportCrontab($name);
    }

    /**
     * 导出表结构
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function exportSql(string $name)
    {
        $sqlPath = base_path() . "/plugin/{$name}/install.sql";
        if (!file_exists($sqlPath)) {
            return;
        }
        $sql = file_get_contents($sqlPath);
        if (empty($sql)) {
            return;
        }
        $tableNames = MysqlUtil::getSqlTableName($sql);
        if (empty($tableNames)) {
            return;
        }
        $prefix = MysqlUtil::getConfig()['prefix'] ?? 'xb_';
        $str = ['`xb_', '`__PREFIX__'];
        // 清空文件
        file_put_contents($sqlPath, '');
        // 遍历表
        foreach ($tableNames as $table) {
            // 替换表前缀
            $table = str_replace($str, "`{$prefix}", $table);
            // 删除表语句
            $tableSql = "-- 删除表语句\nDROP TABLE IF EXISTS `{$table}`;";
            // 获取表结构
            $tablePreviewSql = MysqlUtil::tablePreviewSql($table, false);
            // 拼接删除表语句
            $tableSql = "{$tableSql}\n{$tablePreviewSql}";
            // 追加至文件
            file_put_contents($sqlPath, $tableSql, FILE_APPEND);
        }
    }

    /**
     * 导出菜单
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function exportMenus(string $name)
    {
        $menusPath = base_path() . "/plugin/{$name}/config/menu.php";
        if (!file_exists($menusPath)) {
            return;
        }
        $menus = AdminRule::where('plugin', $name)->select()->toArray();
        if (empty($menus)) {
            return;
        }
        foreach ($menus as $value) {
            if ($value['pid'] === 0) {
                continue;
            }
            $menu = AdminRule::where('id', $value['pid'])->find();
            if ($menu && $menu['pid'] === 0) {
                $ids = array_column($menus, 'id');
                if (in_array($menu['id'], $ids)) {
                    continue;
                }
                $temp = $menu->toArray();
                array_push($menus, $temp);
            }
        }
        // 序列化菜单
        $menus = MenuChecked::serializeMenus($menus);
        // 将菜单二维数组转为多层级
        $menus = MenuChecked::menu2DToTree($menus);
        // 去除不必要的字段
        $menus = MenuChecked::unsetMenusFields($menus, [
            'id', 'pid', '_level', '_html'
        ]);
        // 替换菜单内容
        $content = var_export($menus, true);
        // 替换内容
        $str1 = ['array (', ')'];
        $str2 = ['[', ']'];
        $content = str_replace($str1,$str2, $content);
        // 正则替换
        $content = preg_replace('/\d+ => /', '', $content);
        // 写入文件
        file_put_contents($menusPath, "<?php\n\nreturn " . $content . ";");
    }

    /**
     * 导出字典
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function exportDict(string $name)
    {
        $path = base_path() . "/plugin/{$name}/config/dict.php";
        if (!file_exists($path)) {
            return;
        }
        $dict = include $path;
        if (empty($dict)) {
            return;
        }
        // 检测字典插件是否安装
        if (!class_exists('\plugin\xbDict\api\Install')) {
            return;
        }
        $names = array_column($dict, 'name');
        // 查询字典
        $data = Db::name('dict_tag')->where('name', 'in', $names)->select()->toArray();
    }

    /**
     * 导出定时任务
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function exportCrontab(string $name)
    {
    }
}