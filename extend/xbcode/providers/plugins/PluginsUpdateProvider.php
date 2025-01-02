<?php
namespace xbcode\providers\plugins;

use support\Response;
use app\model\Plugins;
use xbcode\utils\ZipUtil;
use xbcode\utils\MysqlUtil;

/**
 * 插件更新服务提供
 * 1.下载插件
 * 2.备份插件代码
 * 3.备份插件数据
 * 4.解压插件包
 * 5.执行插件安装脚本
 * 6.更新完成
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginsUpdateProvider extends PluginsBaseProvider
{
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
     * 备份忽略文件
     * @var array
     */
    protected $backupIgnoreFile = [];

    /**
     * 开始服务
     * @param string $step
     * @param string $name
     * @param string $versionName
     * @param int $version
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function start(string $step, string $name, string $versionName, int $version)
    {
        // 备份地址总目录
        $backupPath = base_path() . "/backup/plugins/{$name}/{$versionName}";
        // 备份插件文件地址
        $this->backupPluginCodePath = "{$backupPath}.zip";
        // 备份插件数据库地址
        $this->backupPluginSqlPath = "{$backupPath}.sql";
        // 开始服务
        return parent::start($step, $name, $versionName, $version);
    }

    /**
     * 下载插件包
     * @param string $next
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function download(string $next = 'backupCode'): Response
    {
        // 下载插件包
        parent::download($next);
        // 返回数据
        return $this->successRes([
            'next' => $next
        ]);
    }

    /**
     * 备份插件代码
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function backupCode(): Response
    {
        // 检测插件目录是否存在
        if (!is_dir($this->pluginPath)) {
            return $this->fail('插件目录不存在');
        }
        // 检测备份目录是否存在
        $backupCodePath = dirname($this->backupPluginCodePath);
        // 检测备份目录是否存在
        if (!is_dir($backupCodePath)) {
            mkdir($backupCodePath, 0777, true);
        }
        // 备份插件代码
        ZipUtil::build($this->backupPluginCodePath, $this->pluginPath, $this->backupIgnoreFile);
        // 返回数据
        return $this->successRes([
            'next' => 'backupSql',
        ]);
    }

    /**
     * 备份插件数据
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function backupSql(): Response
    {
        // 检测插件目录是否存在
        if (!is_dir($this->pluginPath)) {
            return $this->fail('插件目录不存在');
        }
        // 检测备份目录是否存在
        $backupSqlPath = dirname($this->backupPluginSqlPath);
        // 检测备份目录是否存在
        if (!is_dir($backupSqlPath)) {
            mkdir($backupSqlPath, 0777, true);
        }
        // 表前缀
        $prefix = config('thinkorm.connections.mysql.prefix');
        if (!$prefix) {
            return $this->fail('数据库表前缀不存在');
        }
        // 获取所有数据表名
        $tables = MysqlUtil::getTableNames();
        // 插件表名
        $pluginTableName = "{$prefix}{$this->pluginName}_";
        // 获取插件数据表名
        $pluginTableNames = array_filter($tables, function ($tableName) use ($pluginTableName) {
            // 判断是否插件表
            return strpos($tableName, $pluginTableName) !== false;
        });
        // 重置插件数据表名索引
        $pluginTableNames = array_values($pluginTableNames);
        // 未检测到插件插件表名
        if (empty($pluginTableNames)) {
            // 创建空文件
            file_put_contents($this->backupPluginSqlPath, '/* 插件数据备份,无插件表数据 */');
            // 返回数据
            return $this->successRes([
                'next' => 'unzip',
            ]);
        }
        // 备份插件数据库
        MysqlUtil::exportSql($this->backupPluginSqlPath, true, $pluginTableNames);
        // 替换表前缀
        $content = file_get_contents($this->backupPluginSqlPath);
        foreach ($pluginTableNames as $value) {
            $newTableName = str_replace($prefix, "__PREFIX__", $value);
            $content      = str_replace("`{$value}`", "`{$newTableName}`", $content);
        }
        // 保存替换后的数据
        file_put_contents($this->backupPluginSqlPath, $content);
        // 返回数据
        return $this->successRes([
            'next' => 'unzip',
        ]);
    }

    /**
     * 解压插件包
     * @param string $next
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function unzip(string $next = 'database'): Response
    {
        try {
            // 解压插件包
            parent::unzip($next);
        } catch (\Throwable $th) {
            // 解压失败，执行代码回滚
            $class = new PluginsUpdateRollbackProvider;
            $class->start($this->pluginName, $this->versionName, $this->version);
            // 返回数据
            return $this->successFul('升级出错，代码回滚完成', []);
        }
        // 返回数据
        return $this->successRes([
            'next' => $next
        ]);
    }

    /**
     * 安装数据
     * @param string $next
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function database(string $next = 'success'): Response
    {
        try {
            // 安装类路径
            $classPath = $this->pluginPath . '/api/Install.php';
            if (!file_exists($classPath)) {
                throw new \Exception('插件更新脚本不存在');
            }
            // 重新引入更新类，确保是最新更新类
            require_once $classPath;
            $class = "\\plugin\\{$this->pluginName}\\api\\Install";
            if (class_exists($class)) {
                $classService = new $class(
                    $this->pluginName,
                    $this->versionName,
                    $this->version,
                );
                // 本地插件版本
                $localVersion = $this->localVersion($this->pluginName);
                // 执行前置方法
                $context = null;
                if (method_exists($classService, 'beforeUpdate')) {
                    $context = call_user_func([$classService, 'beforeUpdate'], $localVersion, $this->versionName);
                }
                // 执行方法
                if (method_exists($classService, 'update')) {
                    $context = call_user_func([$classService, 'update'], $localVersion, $this->versionName, $context);
                }
                // 执行方法后置
                if (method_exists($classService, 'afterUpdate')) {
                    call_user_func([$classService, 'afterUpdate'], $localVersion, $this->versionName, $context);
                }
            }
        } catch (\Throwable $th) {
            // 更新失败，回滚代码与数据
            $class = new PluginsUpdateRollbackProvider;
            $class->start($this->pluginName, $this->versionName, $this->version, true);
            // 更新插件缓存
            (new Plugins)->pluginCacheDict(true);
            // 返回数据
            return $this->successFul('插件更新失败，数据回滚完成', []);
        }
        return $this->successRes([
            'next' => $next
        ]);
    }

    /**
     * 更新成功
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function success(): Response
    {
        // 插件更新完成
        Plugins::pluginUpdate($this->pluginName, $this->versionName, $this->version);
        // 更新插件缓存
        (new Plugins)->pluginCacheDict(true);
        // 返回数据
        return $this->successFul('插件更新成功，即将关闭...', [
            'next' => ''
        ]);
    }
}