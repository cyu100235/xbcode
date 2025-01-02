<?php
namespace xbcode\providers\plugins;

use support\Response;
use app\model\Plugins;
use xbcode\utils\DirUtil;

/**
 * 插件卸载服务
 * 1.卸载数据
 * 2.删除代码
 * 3.更新完成
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginsUninstallProvider extends PluginsBaseProvider
{
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
        return parent::start($step, $name, $versionName, $version);
    }

    /**
     * 删除插件数据库
     * @param string $next
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function database(string $next = 'delCode'): Response
    {
        // 安装类路径
        $classPath = $this->pluginPath . '/api/Install.php';
        if (!file_exists($classPath)) {
            throw new \Exception('插件卸载脚本不存在');
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
            if (method_exists($classService, 'beforeUninstall')) {
                $context = call_user_func([$classService, 'beforeUninstall']);
            }
            // 执行方法
            if (method_exists($classService, 'uninstall')) {
                $context = call_user_func([$classService, 'uninstall'], $this->versionName, $context);
            }
            // 执行后置方法
            if (method_exists($classService, 'afterUnistall')) {
                call_user_func([$classService, 'afterUnistall'], $this->versionName, $context);
            }
        }
        return $this->successRes([
            'next' => $next,
        ]);
    }

    /**
     * 删除插件代码
     * @param string $next
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function delCode(string $next = 'success'): Response
    {
        // 删除插件代码
        DirUtil::delDir($this->pluginPath);
        // 返回数据
        return $this->successRes([
            'next' => $next,
        ]);
    }

    /**
     * 插件卸载成功
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function success(): Response
    {
        // 获取本地版本
        $localVersionName = $this->localVersion($this->pluginName);
        // 卸载插件记录
        $where = [
            'name' => $this->pluginName,
            'version_name' => $localVersionName,
        ];
        Plugins::where($where)->delete();
        // 更新插件缓存
        (new Plugins)->pluginCacheDict(true);
        // 返回数据
        return $this->successFul('插件卸载完成，即将关闭...', [
            'next' => '',
        ]);
    }
}