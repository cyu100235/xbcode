<?php
namespace plugin\xbDeveloper\base;

use Exception;
use plugin\xbCode\utils\trait\JsonTrait;

/**
 * 插件服务基类提供
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
abstract class BasePlugins
{
    // 引入JsonTrait
    use JsonTrait;

    /**
     * 插件标识
     * @var string
     */
    protected $pluginName;

    /**
     * 版本名称
     * @var string
     */
    protected $versionName;

    /**
     * 版本编号
     * @var int
     */
    protected $version;

    /**
     * 插件目录
     * @var string
     */
    protected $pluginPath;

    /**
     * 插件包文件路径
     * @var string
     */
    protected $packagePath;
    
    /**
     * 开始服务
     * @param string $step 执行步骤
     * @param string $name 插件标识
     * @param string $versionName 版本名称
     * @param int $version 版本编号
     * @param string $method 执行安装方法
     * @return mixed
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function start(string $step, string $name, string $versionName, int $version, string $method = '')
    {
        if (!method_exists($this, $step)) {
            return $this->fail('执行步骤错误');
        }
        // 插件标识
        $this->pluginName = $name;
        // 目标版本名称
        $this->versionName = $versionName;
        // 目标版本编号
        $this->version = $version;
        // 插件目录
        $this->pluginPath = base_path()."/plugin/{$name}";
        // 插件包路径
        $this->packagePath = runtime_path()."/plugin/{$name}-{$versionName}.zip";
        // 执行更新步骤
        if ($method) {
            return call_user_func([$this, $step], $method);
        }
        return call_user_func([$this, $step]);
    }

    /**
     * 执行安装脚本
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function script()
    {
        // 获取类命名空间
        $class = get_called_class();
        // 替换类地址
        $class = str_replace('\\', '/', $class);
        // 获取类名
        $class = basename($class);
        // 获取方法名
        $method = str_replace('Plugins', '', $class);
        // 方法转小写
        $method = strtolower($method);
        // 安装类路径
        $classPath = $this->pluginPath . '/api/Install.php';
        if (!file_exists($classPath)) {
            throw new Exception('插件脚本文件不存在');
        }
        // 重新引入更新类，确保是最新更新类
        require_once $classPath;
        $class = "\\plugin\\{$this->pluginName}\\api\\Install";
        if (class_exists($class)) {
            // 执行前置方法
            $context = null;
            if (method_exists($class, "{$method}Before")) {
                $context = call_user_func([$class, "{$method}Before"], $this->versionName);
            }
            // 执行方法
            if (method_exists($class, $method)) {
                $context = call_user_func([$class, $method], $this->versionName, $context);
            }
            // 执行后置方法
            if (method_exists($class, "{$method}After")) {
                call_user_func([$class, "{$method}After"], $this->versionName, $context);
            }
        }
    }

    /**
     * 执行安装方法
     * @param string $method
     * @throws \Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function installMethod(string $method)
    {
        // 安装类路径
        $classPath = $this->pluginPath . '/api/Install.php';
        if (!file_exists($classPath)) {
            throw new Exception('插件脚本文件不存在');
        }
        // 重新引入更新类，确保是最新更新类
        require_once $classPath;
        $class = "\\plugin\\{$this->pluginName}\\api\\Install";
        if (class_exists($class)) {
            // 执行方法
            $context = null;
            if (method_exists($class, "installMethod")) {
                call_user_func([$class, "installMethod"], $method);
            }
        }
    }

    /**
     * 操作完成
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    abstract protected function complete();
}