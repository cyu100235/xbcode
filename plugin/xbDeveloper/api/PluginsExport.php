<?php
/**
 * 积木云渲染器
 * @package  XbCode.0
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbDeveloper\api;

use Exception;
use support\Log;
use support\think\Db;
use plugin\xbCode\api\Mysql;
use Brick\VarExporter\VarExporter;
use plugin\xbCode\api\MenuChecked;
use plugin\xbCrontab\api\CrontabApi;
use plugin\xbCode\app\model\AdminRule;

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
        $tableNames = Mysql::getSqlTableName($sql);
        if (empty($tableNames)) {
            return;
        }
        // 获取当前项目表前缀
        $prefix = Mysql::getConfig()['connections']['mysql']['prefix'] ?? 'xb_';
        // 单个模板前缀
        $templatePrefix = 'xb_';
        // 将模板表名替换为当前项目表前缀
        $tableNames = array_map(function ($table) use ($prefix, $templatePrefix) {
            return str_replace($templatePrefix, $prefix, $table);
        }, $tableNames);
        // 表安装SQL
        $tableSql = [];
        // 遍历表
        foreach ($tableNames as $table) {
            // 检测表是否存在
            if (!Mysql::hasTable($table)) {
                continue;
            }
            // 替换表名（无前缀）
            $tableName = str_replace($prefix, $templatePrefix, $table);
            // 获取表结构
            $tablePreviewSql = Mysql::tablePreviewSql($table, false);
            // 将当前项目表前缀替换为模板表前缀
            $tablePreviewSql = str_replace("`{$prefix}", "`{$templatePrefix}", $tablePreviewSql);
            // 替换自增键始终为1
            if (str_contains($tablePreviewSql, 'AUTO_INCREMENT=')) {
                $tablePreviewSql = preg_replace('/AUTO_INCREMENT=\d+/', 'AUTO_INCREMENT=1', $tablePreviewSql);
            } else {
                $tablePreviewSql = str_replace('ENGINE=InnoDB', 'ENGINE=InnoDB AUTO_INCREMENT=1', $tablePreviewSql);
            }
            // 删除表语句
            $sql = "-- 删除表语句\nDROP TABLE IF EXISTS `{$tableName}`;";
            // 拼接删除表语句
            $sql = "{$sql}\n{$tablePreviewSql}";
            // 添加至表安装SQL
            $tableSql[] = $sql;
        }
        if ($tableSql) {
            // 数组转字符串
            $tableSql = implode("\n", $tableSql);
            // 保存文件
            file_put_contents($sqlPath, $tableSql);
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
            'id',
            'pid',
            '_level',
            '_html'
        ]);
        // 格式化数组菜单字符串
        $content = VarExporter::export($menus);
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
        // 检测字典插件是否安装
        if (!class_exists('\plugin\xbCrontab\api\Install')) {
            return;
        }
        try {
            CrontabApi::make()->exportPlugin($name);
        } catch (\Throwable $th) {
            Log::error("开发辅助导出定时任务失败: {$th->getMessage()}");
        }
    }
}