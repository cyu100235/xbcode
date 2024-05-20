<?php
namespace app\service\action;

use app\service\cloud\HttpCloud;
use app\service\CloudSerivce;
use app\utils\FrameUtil;
use app\utils\JsonUtil;
use app\utils\ZipUtil;
use think\facade\Db;
use Exception;

/**
 * 插件通用基类类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginBaseAction
{
    use JsonUtil;
    
    /**
     * 下载插件
     * @param string $name
     * @param string $version
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function downloadFile(string $name,string $version)
    {
        // 请求云端下载
        $data   = [
            'name'      => $name,
            'version'   => $version
        ];
        $result = HttpCloud::get('user/Plugins/download', $data);
        // 获取结果集
        $content = HttpCloud::getContent($result, false);
        // 临时插件包路径
        $package       = base_path("runtime/plugin/") . "{$name}-{$version}.zip";
        // 压缩包存在则删除
        if (file_exists($package)) {
            unlink($package);
        }
        // 写入文件
        file_put_contents($package, $content);
    }
    
    /**
     * 解压插件
     * @param string $name
     * @param string $version
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function unzipFile(string $name,string $version)
    {
        try {
            // 临时插件包路径
            $package       = base_path("runtime/plugin/") . "{$name}-{$version}.zip";
            // 插件目录
            $pluginDir       = base_path("plugin/{$name}");
            // 暂停文件监控
            FrameUtil::pauseFileMonitor();
            // 检测目录不存在则创建
            if (!is_dir($pluginDir)) {
                mkdir($pluginDir, 0755, true);
            }
            // 解压插件包
            ZipUtil::unzip($package, $pluginDir);
            // 删除插件包
            unlink($package);
            // 恢复文件监控
            FrameUtil::resumeFileMonitor();
        } catch (\Throwable $th) {
            // 删除插件包
            unlink($package);
            throw $th;
        }
    }
    
    /**
     * 安装依赖
     * @param string $name
     * @param string $version
     * @param string $methodName
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function installDepend(string $name,string $version, string $methodName)
    {
        // 获取插件依赖项
        $data = CloudSerivce::getLocalPluginDepend($name);
        // 开启事务
        Db::startTrans();
        try {
            // 安装依赖
            foreach ($data as $plugin => $version) {
                // 下载插件
                self::downloadFile($plugin, $version);
                // 解压插件
                self::unzipFile($plugin, $version);
                // 数据安装
                self::installData($plugin, $version, $methodName);
            }
            // 提交事务
            Db::commit();
        } catch (\Throwable $th) {
            // 回滚事务
            Db::rollback();
            // 删除依赖插件目录
            foreach ($data as $plugin => $version) {
                // 删除插件目录
                $dependDir = base_path("plugin/{$plugin}");
                if (is_dir($dependDir)) {
                    remove_dir($dependDir);
                }
            }
            // 删除插件目录
            $pluginDir = base_path("plugin/{$name}");
            if (is_dir($pluginDir)) {
                remove_dir($pluginDir);
            }
            throw $th;
        }
    }
    
    /**
     * 数据安装
     * @param string $name
     * @param string $version
     * @param string $type
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function installData(string $name,string $version,string $methodName)
    {
        // 插件目录
        $pluginDir       = base_path("plugin/{$name}");
        try {
            // 插件类命名空间
            $class = "\\plugin\\{$name}\\Install";
            // 检测类是否存在
            if (!class_exists($class)) {
                throw new Exception("安装类错误：{$class}");
            }
            // 上下文
            $context = null;
            // 前置
            if (method_exists($class, "{$methodName}Before")) {
                $context = call_user_func([new $class, "{$methodName}Before"]);
            }
            // 插件
            if (method_exists($class, $methodName)) {
                $context = call_user_func([new $class, $methodName], $context);
            }
            // 后置
            if (method_exists($class, "{$methodName}After")) {
                call_user_func([new $class, "{$methodName}After"], $context);
            }
        } catch (\Throwable $th) {
            remove_dir($pluginDir);
            throw $th;
        }
    }
    
    /**
     * 安装完成
     * @param string $name
     * @param string $version
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function installOk(string $name,string $version)
    {
        // 重启框架
        FrameUtil::pcntlAlarm(1, function () {
            FrameUtil::reload();
        });
    }
}