<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\base;

use Exception;
use support\Log;
use plugin\xbCode\api\Composer;
use plugin\xbCode\api\Packages;
use plugin\xbCode\api\PluginsApi;
use plugin\xbCode\base\plugin\UpdateTrait;
use plugin\xbCode\base\plugin\InstallTrait;
use plugin\xbCode\base\plugin\UnInstallTrait;

/**
 * 插件基类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
abstract class BasePlugin
{
    // 插件安装
    use InstallTrait;
    // 插件更新
    use UpdateTrait;
    // 插件卸载
    use UnInstallTrait;

    /**
     * 插件名称
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $title;

    /**
     * 插件版本号
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $version;

    /**
     * 插件标识
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $name;

    /**
     * 插件作者
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $author;

    /**
     * 插件Composer依赖
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $composer;

    /**
     * 插件依赖的其他插件
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $plugins;

    /**
     * 插件描述
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $desc;

    /**
     * 构造函数
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function __construct()
    {
        // 获取插件名称
        $name = $this->getCallPluginName();
        // 获取插件信息
        $plugin = PluginsApi::make()->get($name);
        $this->name = $plugin['name'];
        $this->title = $plugin['title'];
        $this->version = $plugin['version'];
        $this->author = $plugin['author'];
        $this->desc = $plugin['desc'];
        $this->composer = $plugin['composer'];
        $this->plugins = $plugin['plugins'];
    }

    /**
     * 执行对应的安装方法
     * @param string $method
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function installMethod(string $method)
    {
        // 方法名首字母转大写
        $method = ucfirst($method);
        // 方法名称
        $methodName = "install{$method}";
        if (!method_exists(static::class, $methodName)) {
            throw new Exception('执行安装方法错误');
        }
        return call_user_func([static::class, $methodName]);
    }

    /**
     * 安装前置
     * @param string $version 版本名称
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function installBefore(string $version)
    {
        try {
            // 检测依赖是否安装
            Packages::composer($this->name);
        } catch (\Throwable $th) {
            if ($th->getCode() === 4) {
                Composer::install($this->name);
            }
        }
        try {
            // 检查插件是否安装
            Packages::plugins($this->name);
        } catch (\Throwable $th) {
            if ($th->getCode() !== 1) {
                throw $th;
            }
        }
    }

    /**
     * 安装
     * @param string $version 版本名称
     * @param mixed $context 从<安装之前>返回的上下文
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function install(string $version, mixed $context = null)
    {
        try {
            // 安装数据库
            $this->installSql();
            // 安装配置
            $this->installConfig();
            // 安装菜单
            $this->installMenus();
            // 安装字典
            $this->installDict();
            // 安装定时任务
            $this->installCrontab();
        } catch (\Throwable $th) {
            // 尝试卸载插件数据
            try {
                $this->uninstall($version, $context);
            } catch (\Throwable $th) {
                throw new Exception("卸载插件出错：{$th->getMessage()}");
            }
            Log::error("安装插件出错：{$th->getMessage()}");
            throw $th;
        }
        // 返回数据给安装
        return [];
    }

    /**
     * 安装后置
     * @param string $version 版本名称
     * @param mixed $context 从<安装>返回的上下文
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function installAfter(string $version, mixed $context = null)
    {
        // 可以自己实现安装之后的业务逻辑...
    }

    /**
     * 更新前置
     * @param string $version 版本名称
     * @return mixed
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function updateBefore(string $version)
    {
        // 返回数据给更新
        return [];
    }

    /**
     * 更新
     * @param string $version 版本名称
     * @param mixed $context 从<安装>返回的上下文
     * @return mixed
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function update(string $version, mixed $context = null)
    {
        try {
            // 执行版本SQL
            $this->updateSQL();
            // 返回上下文
            return $context;
        } catch (\Throwable $th) {
            Log::error("更新插件出错：{$th->getMessage()}");
            throw $th;
        }
    }

    /**
     * 更新后置
     * @param string $version 版本名称
     * @param mixed $context 从<安装>返回的上下文
     * @return mixed
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function updateAfter(string $version, mixed $context = null)
    {
        return $context;
    }

    /**
     * 卸载前置
     * @param string $version 版本名称
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function uninstallBefore(string $version): array
    {
        // 检查是否有依赖插件
        PluginsApi::make()->hasPluginDependThrow($this->name);
        // 返回数据给卸载
        return [];
    }

    /**
     * 卸载
     * @param string $version 版本名称
     * @param mixed $context 从<卸载之前>返回的上下文
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function uninstall(string $version, mixed $context = null)
    {
        try {
            // 删除配置
            $this->uninstallConfig();
            // 删除菜单
            $this->uninstallMenus();
            // 删除字典
            $this->uninstallDict();
            // 删除定时任务
            $this->uninstallCrontab();
            // 删除数据库
            $this->uninstallSql();
        } catch (\Throwable $th) {
            Log::error("卸载插件数据出错：{$th->getMessage()}");
            throw $th;
        }
        // 返回数据给卸载
        return [];
    }

    /**
     * 卸载后置
     * @param string $version 版本名称
     * @param mixed $context 从<卸载>返回的上下文
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function uninstallAfter(string $version, mixed $context = null)
    {
    }

    /**
     * 获取配置项
     * @param string $name 插件标识
     * @return array
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getPanelConfig(string $name)
    {
        $path = base_path() . "/plugin/{$name}/setting/tabs/panel.php";
        if (!file_exists($path)) {
            return [];
        }
        $config = include $path;
        if (empty($config)) {
            return [];
        }
        if (!is_array($config)) {
            return [];
        }
        return $config;
    }

    /**
     * 获取调用类插件名称
     * @throws Exception
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function getCallPluginName()
    {
        // 获取调用类命名空间
        $callClass = get_called_class();
        // 反射获取子类的文件路径
        $reflectionClass = new \ReflectionClass($callClass);
        $childFilePath = $reflectionClass->getFileName();
        // 获取插件名称
        $name = basename(dirname($childFilePath, 2));
        // 返回插件名称
        return $name;
    }
}