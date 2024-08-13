<?php
namespace plugin\{PLUGIN_NAME};

use app\common\providers\MenuProvider;
use app\common\providers\MysqlProvider;
use app\common\providers\DictProvider;

/**
 * 插件安装卸载类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class Install
{
    /**
     * 安装前
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function installBefore()
    {
        // 可以自己实现安装之前的业务逻辑...
    }
    
    /**
     * 安装
     * @param mixed $context 从<安装前>返回的上下文
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function install(mixed $context)
    {
        // 创建菜单
        $this->createMenus();
        // 创建字典
        $this->createDicts();
        
        // 导入安装SQL
        $sql = __DIR__ . '/data/sql/install.sql';
        if (file_exists($sql)) {
            MysqlProvider::importSql($sql);
        }
        // 有其他业务逻辑可以在这个方法里面继续写
    }

    /**
     * 安装后
     * @param mixed $context 从<安装>返回的上下文
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function installAfter(mixed $context)
    {
        // 可以自己实现安装之后的业务逻辑...
    }

    /**
     * 更新前
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function updateBefore()
    {
        // 导入更新SQL
        $sql = __DIR__ . '/data/sql/update.sql';
        if (file_exists($sql)) {
            MysqlProvider::importSql($sql);
        }
    }

    /**
     * 更新
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function update(mixed $context)
    {
    }

    /**
     * 更新后
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function updateAfter(mixed $context)
    {
    }

    /**
     * 卸载前
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function uninstallBefore()
    {
    }

    /**
     * 卸载
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function uninstall(mixed $context)
    {
        // 卸载菜单数据
        $this->delMenu();
        
        // 批量删除字典
        $dicts = config('plugin.{PLUGIN_NAME}.tpldata.dict', []);
        DictProvider::delDicts($dicts);

        // 导入卸载SQL
        $sql = __DIR__ . '/data/sql/uninstall.sql';
        if (file_exists($sql)) {
            MysqlProvider::importSql($sql);
        }
    }

    /**
     * 卸载后
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function uninstallAfter()
    {
    }
    
    /**
     * 创建字典
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function createDicts()
    {
        // 获取字典数据
        $data = config('plugin.{PLUGIN_NAME}.tpldata.dict', []);
        if (empty($dicts)) {
            return true;
        }
        // 批量创建字典
        DictProvider::addDicts($data);
        return true;
    }

    /**
     * 创建菜单
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function createMenus()
    {
        // 获取菜单数据
        $menus = config('plugin.{PLUGIN_NAME}.tpldata.menus', []);
        if (empty($menus)) {
            return true;
        }
        // 批量创建菜单
        MenuProvider::createMenus($menus);
        return true;
    }

    /**
     * 删除菜单数据
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function delMenu(mixed $context)
    {
        // 获取菜单数据
        $menus = config('plugin.{PLUGIN_NAME}.tpldata.menus', []);
        if ($menus) {
            // 执行倒序
            $menus = array_reverse($menus);
            // 获取菜单KEY
            $keys = array_column($menus, 'path');
            // 删除菜单
            MenuProvider::delMenus($keys);
        }
    }
}