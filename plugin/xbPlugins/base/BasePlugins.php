<?php
namespace plugin\xbPlugins\base;

use Exception;
use plugin\xbCode\utils\DirUtil;
use plugin\xbCode\utils\ZipUtil;
use plugin\xbPlugins\api\PluginsApi;
use plugin\xbPlugins\app\model\Plugins;
use plugin\xbCode\utils\trait\JsonTrait;

/**
 * 插件服务基类提供
 * @copyright 贵州小白基地网络科技有限公司
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
     * 是否本地导入
     * @var bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $isLocal = false;

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
     * @param bool $isLocal 是否本地导入
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function start(string $step, string $name, string $versionName, int $version,bool $isLocal = false)
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
        // 是否本地导入
        $this->isLocal = $isLocal;
        // 插件目录
        $this->pluginPath = base_path()."/plugin/{$name}";
        // 插件包路径
        $this->packagePath = runtime_path()."/plugin/{$name}-{$versionName}.zip";
        // 执行更新步骤
        return call_user_func([$this, $step]);
    }
    
    /**
     * 下载插件包
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function download()
    {
        // 检测插件包储存目录是否存在
        $pluginPath = dirname($this->packagePath);
        if (!is_dir($pluginPath)) {
            mkdir($pluginPath, 0777, true);
        }
        // 检测文件是否存在
        if (file_exists($this->packagePath)) {
            unlink($this->packagePath);
        }
        // 下载更新包
        // $content = PluginService::download($this->pluginName, $this->versionName, $this->version);
        $content = '';
        // 写入文件
        file_put_contents($this->packagePath, $content);
    }
    
    /**
     * 解压插件包
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function unzip()
    {
        // 检测插件包是否存在
        if (!file_exists($this->packagePath)) {
            throw new Exception('插件包不存在');
        }
        // 检测插件目录是否存在
        if (!is_dir($this->pluginPath)) {
            mkdir($this->pluginPath, 0777, true);
        }
        try {
            // 解压更新包
            ZipUtil::unzip($this->packagePath, $this->pluginPath);
        } catch (\Throwable $th) {
            // 检测插件包是否存在
            if (DirUtil::isDirEmpty($this->pluginPath)) {
                DirUtil::delDir($this->pluginPath);
            }
            throw new Exception("解压插件包失败:{$th->getMessage()}");
        }
        // 解压完成，删除插件包
        if (file_exists($this->packagePath)) {
            unlink($this->packagePath);
        }
    }
    
    /**
     * 获取插件本地版本
     * @param string $name
     * @param string $field
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function localVersion(string $name, string $field = 'version_name')
    {
        $version = Plugins::where('name', $name)->value($field);
        return $version ?: '';
    }

    /**
     * 执行安装脚本
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
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
     * 操作完成
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    abstract protected function complete();

    /**
     * 新增安装记录
     * @throws \Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function installed()
    {
        PluginsApi::addRecord($this->pluginName, $this->isLocal ? '20' : '10');
    }
}