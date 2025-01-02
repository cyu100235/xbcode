<?php
namespace xbcode\providers\plugins;

use app\model\Plugins;
use support\Response;

/**
 * 插件安装服务
 * 1.下载插件
 * 2.解压更新包
 * 3.安装数据库
 * 4.更新完成
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginsInstallProvider extends PluginsBaseProvider
{
    /**
     * 安装数据
     * @param string $next 下一步名称
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
                throw new \Exception('插件安装脚本不存在');
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
                // 执行前置方法
                $context = [];
                if (method_exists($classService, 'beforeInstall')) {
                    $context = call_user_func([$classService, 'beforeInstall'], $this->versionName);
                }
                // 执行方法
                if (method_exists($classService, 'install')) {
                    $context = call_user_func([$classService, 'install'], $this->versionName, $context);
                }
                // 执行后置方法
                if (method_exists($classService, 'afterInstall')) {
                    call_user_func([$classService, 'afterInstall'], $this->versionName, $context);
                }
            }
        } catch (\Throwable $th) {
            // 解压失败，执行代码与数据回滚
            $class = new PluginsInstallRollbackProvider;
            $class->start($this->pluginName, $this->versionName, $this->version);
            // 更新插件缓存
            (new Plugins)->pluginCacheDict(true);
            // 抛出错误
            throw $th;
        }
        return $this->successRes([
            'next' => $next
        ]);
    }

    /**
     * 安装成功
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function success(): Response
    {
        // 设置插件安装
        Plugins::pluginInstall($this->pluginName, $this->versionName, $this->version);
        // 更新插件缓存
        (new Plugins)->pluginCacheDict(true);
        // 返回成功
        return $this->successFul('插件安装成功，即将关闭...', [
            'next' => ''
        ]);
    }
}