<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @version  1.0.0
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbDeveloper\api;

use Exception;
use plugin\xbCode\api\Menus;
use plugin\xbCode\api\Mysql;
use plugin\xbCode\api\DebugApi;
use plugin\xbCode\api\PluginsApi;
use plugin\xbCode\api\MenuChecked;

/**
 * 开发辅助接口类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class DevelopmentApi
{
    /**
     * 创建实例
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function make()
    {
        $class = new static;
        return $class;
    }

    /**
     * 获取插件列表
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getList()
    {
        $pluginAPI = PluginsApi::make();
        // 刷新缓存
        $pluginAPI->getCache(true);
        $data = $pluginAPI->getList();
        $data = array_map(function ($value) {
            // 检测是否可以打包
            $value['can_package'] = PatchPackageApi::make()->canPackage($value['name']) ? '20' : '10';
            // 是否可以推送仓库
            $value['can_push'] = DepositoryApi::make($value['name'])->isGitRepo() ? '20' : '10';
            return $value;
        }, $data);
        return $data;
    }

    /**
     * 创建插件
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function create(array $data)
    {
        // 数据验证
        PluginsCreate::validate($data);
        // 是否调试模式
        $debug = DebugApi::status();
        // 创建插件
        PluginsCreate::create($data, $debug);
    }

    /**
     * 执行安装文件SQL
     * @param string $name 插件标识
     * @throws Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function sql(string $name)
    {
        if (!$name) {
            throw new Exception('插件标识参数错误');
        }
        // 获取SQL文件
        $file = base_path() . "/plugin/{$name}/install.sql";
        if (!file_exists($file)) {
            throw new Exception('插件SQL文件不存在');
        }
        // 执行SQL脚本文件
        Mysql::importSql($file, ['xb_', '__PREFIX__']);
    }

    /**
     * 导出插件
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function export(string $name, string $method)
    {
        if (empty($method)) {
            throw new Exception('执行导出参数错误');
        }
        if (empty($name)) {
            throw new Exception('插件标识参数错误');
        }
        if (!class_exists(PluginsExport::class)) {
            throw new Exception('插件导出类不存在');
        }
        // 首字母转大写
        $method = ucfirst($method);
        // 拼接方法名
        $method = "export{$method}";
        $class = new PluginsExport;
        if (!method_exists($class, $method)) {
            throw new Exception('插件导出方法不存在');
        }
        // 执行导出
        call_user_func([$class, $method], $name);
    }

    /**
     * 导出插件菜单至插件配置
     * @param string $name 插件标识
     * @throws Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function menus(string $name)
    {

        if (!DebugApi::status()) {
            throw new Exception('请先开启开发调试模式');
        }
        // 获取菜单文件
        $file = base_path() . "/plugin/{$name}/config/menu.php";
        if (!file_exists($file)) {
            throw new Exception('插件菜单文件不存在');
        }
        // 获取菜单数据
        $data = include $file;
        // 检测菜单数据
        if (empty($data)) {
            throw new Exception('插件菜单数据为空');
        }
        // 处理菜单数据
        $data = MenuChecked::resetField($data, 'state', '20');
        // 开始安装更新菜单
        Menus::install($data, $name);
    }

    /**
     * 重建所有插件菜单
     * 清空菜单表并重置自增ID后，重新导入所有插件的菜单文件
     * @throws Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function recreateMenus()
    {
        if (!DebugApi::status()) {
            throw new Exception('请先开启开发调试模式');
        }
        Menus::recreate();
    }

    /**
     * 克隆插件仓库
     * @param array $data 表单数据
     * - `url` 仓库ssh地址
     * - `title` 插件名称
     * - `name` 插件标识
     * - `desc` 插件描述
     * - `author` 作者名称
     * @throws Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function clone(array $data)
    {
        if (!DebugApi::status()) {
            throw new Exception('请先开启开发调试模式');
        }
        if (empty($data)) {
            throw new Exception('插件数据参数错误');
        }
        $debug = DebugApi::status();
        PluginsCreate::clone($data, $debug);
    }
}
