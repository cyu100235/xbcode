<?php
namespace plugin\{PLUGIN_NAME}\api;

use xbcode\PluginBase;
use xbcode\utils\MysqlUtil;
use xbcode\providers\DictProvider;
use xbcode\providers\MenuProvider;

/**
 * 插件操作类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class Install extends PluginBase
{
    
    /**
     * 安装之前
     * @param string $version 版本名称
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function installBefore(string $version): array
    {
        // 可以自己实现安装之前的业务逻辑...
        return [];
    }

    /**
     * 安装
     * @param string $version 版本名称
     * @param mixed $context 从<安装之前>返回的上下文
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function install(string $version, mixed $context): array
    {
        // 创建菜单
        $this->createMenus();
        // 创建字典
        $this->createDicts();
        
        // 导入安装SQL
        $sql = dirname(__DIR__) . '/data/sql/install.sql';
        if (file_exists($sql)) {
            MysqlUtil::importSql($sql);
        }
        // 返回数据给安装后
        return [];
    }

    /**
     * 安装之后
     * @param string $version 版本名称
     * @param mixed $context 从<安装>返回的上下文
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function installAfter(string $version, mixed $context)
    {
        // 可以自己实现安装之后的业务逻辑...
    }

    /**
     * 更新前
     * @param string $localVersion 本地版本
     * @param string $toVersion 更新版本
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function updateBefore(string $localVersion, string $toVersion): array
    {
        // 返回数据给更新
        return [];
    }

    /**
     * 更新
     * @param string $localVersion 本地版本
     * @param string $toVersion 更新版本
     * @param mixed $context 从<安装>返回的上下文
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function update(string $localVersion, string $toVersion, mixed $context)
    {
        // 导入更新SQL
        $sql = dirname(__DIR__) . '/data/sql/update.sql';
        if (file_exists($sql)) {
            MysqlUtil::importSql($sql);
        }
        // 返回数据给更新后
        return [];
    }

    /**
     * 更新后
     * @param string $localVersion 本地版本
     * @param string $toVersion 更新版本
     * @param mixed $context 从<安装>返回的上下文
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function updateAfter(string $localVersion, string $toVersion, mixed $context)
    {
    }

    /**
     * 卸载之前
     * @param string $localVersion 本地版本
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function uninstallBefore(string $localVersion): array
    {
        // 返回数据给卸载
        return [];
    }

    /**
     * 卸载
     * @param string $localVersion 本地版本
     * @param mixed $context 从<卸载之前>返回的上下文
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function uninstall(string $localVersion, mixed $context): array
    {
        // 卸载菜单数据
        $this->delMenu();
        // 删除字典数据
        $this->delDicts();

        // 导入卸载SQL
        $sql = dirname(__DIR__) . '/data/sql/uninstall.sql';
        if (file_exists($sql)) {
            MysqlUtil::importSql($sql);
        }
        // 返回数据给卸载后
        return [];
    }

    /**
     * 卸载后
     * @param string $localVersion 本地版本
     * @param mixed $context 从<卸载>返回的上下文
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function uninstallAfter(string $localVersion, mixed $context)
    {
        // 可以自己实现卸载之后的业务逻辑...
    }
    
    /**
     * 创建字典
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function createDicts()
    {
        $path = dirname(__DIR__) . '/data/config/dict.php';
        if (!file_exists($path)) {
            return;
        }
        // 获取字典数据
        $data = require $path;
        if (empty($dicts)) {
            return;
        }
        // 批量创建字典
        foreach ($data as $value) {
            if (empty($value['name'])) {
                continue;
            }
            // 创建字典标签
            DictProvider::tagAction()->addTag($value['name'], $value['title'], true);
            // 批量创建字典数据
            if (!empty($value['children'])) {
                foreach ($value['children'] as $v) {
                    if (empty($v['name'])) {
                        continue;
                    }
                    if (empty($v['title'])) {
                        continue;
                    }
                    // 创建字典数据
                    DictProvider::dataAction()->addTagData(
                        $value['name'],
                        $v['title'],
                        $v['title'],
                        true
                    );
                }
            }
        }
    }
    
    /**
     * 删除字典数据
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function delDicts()
    {
        $path = dirname(__DIR__) . '/data/config/dict.php';
        if (!file_exists($path)) {
            return;
        }
        // 批量删除字典
        $dicts = require $path;
        if (empty($dicts)) {
            return;
        }
        foreach ($dicts as $value) {
            // 删除字典标签
            DictProvider::tagAction()->delTag($value);
            // 删除字典数据
            DictProvider::dataAction()->delTagData($value);
        }
    }
    
    /**
     * 创建菜单
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function createMenus()
    {
        $path = dirname(__DIR__) . '/data/config/menus.php';
        if (!file_exists($path)) {
            return;
        }
        // 获取菜单数据
        $menus = require $path;
        if (empty($menus)) {
            return;
        }
        // 批量创建菜单
        MenuProvider::createMenus($menus);
    }
    
    /**
     * 删除菜单数据
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function delMenu()
    {
        $path = dirname(__DIR__) . '/data/config/menus.php';
        if (!file_exists($path)) {
            return;
        }
        // 获取菜单数据
        $menus = require $path;
        if (empty($menus)) {
            return;
        }
        // 执行倒序
        $menus = array_reverse($menus);
        // 获取菜单KEY
        $keys = array_column($menus, 'path');
        // 删除菜单
        MenuProvider::delMenus($keys);
    }
}