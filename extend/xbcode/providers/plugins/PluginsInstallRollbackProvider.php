<?php
namespace xbcode\providers\plugins;

use Exception;
use xbcode\utils\DirUtil;
use xbcode\utils\MysqlUtil;

/**
 * 插件安装回滚服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginsInstallRollbackProvider
{
    /**
     * 插件名称
     * @var string
     */
    protected $pluginName;

    /**
     * 插件版本名称
     * @var string
     */
    protected $versionName;

    /**
     * 插件版本编号
     * @var int
     */
    protected $version;

    /**
     * 插件目录
     * @var string
     */
    protected $pluginPath;

    /**
     * 备份插件代码文件地址
     * @var string
     */
    protected $backupPluginCodePath;

    /**
     * 备份插件数据库地址
     * @var string
     */
    protected $backupPluginSqlPath;

    /**
     * 开始回滚
     * @param string $name 插件名称
     * @param string $versionName 插件版本名称
     * @param int $version 插件版本编号
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function start(string $name, string $versionName, int $version)
    {
        // 插件名称
        $this->pluginName = $name;
        // 插件版本名称
        $this->versionName = $versionName;
        // 插件版本编号
        $this->version = $version;
        // 插件目录
        $this->pluginPath = base_path() . "/plugin/{$name}";
        // 备份地址总目录
        $backupPath = base_path() . "/backup/plugins/{$name}/{$versionName}";
        // 备份插件文件地址
        $this->backupPluginCodePath = "{$backupPath}.zip";
        // 备份插件数据库地址
        $this->backupPluginSqlPath = "{$backupPath}.sql";
        try {
            // 执行代码回滚
            $this->rollbackCode();
            // 执行数据回滚
            $this->rollbackSql();
        } catch (\Throwable $th) {
            throw new Exception("插件安装回滚出错：{$th->getMessage()}");
        }
    }
    
    /**
     * 删除插件代码目录
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function rollbackCode()
    {
        // 检测插件目录是否存在
        if (!is_dir($this->pluginPath)) {
            throw new Exception("插件目录不存在");
        }
        // 删除插件目录
        DirUtil::delDir($this->pluginPath);
    }

    /**
     * 执行数据回滚
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function rollbackSql()
    {
        // 当前表前缀
        $prefix = config('thinkorm.connections.mysql.prefix');
        // 检测表前缀是否存在
        if (empty($prefix)) {
            throw new Exception("表前缀不存在");
        }
        // 获取所有数据表名
        $tables = MysqlUtil::getTableNames();
        // 插件表名
        $pluginTableName = "{$prefix}{$this->pluginName}_";
        // 获取插件数据表名
        $pluginTableNames = array_filter($tables, function ($tableName) use($pluginTableName){
            // 判断是否插件表
            return strpos($tableName, $pluginTableName) !== false;
        });
        // 重置插件数据表名索引
        $pluginTableNames = array_values($pluginTableNames);
        // 删除插件数据表
        if ($pluginTableNames) {
            MysqlUtil::execute("DROP TABLE IF EXISTS " . implode(',', $pluginTableNames));
        }
    }
}