<?php
namespace app\common\service\action;

use support\Request;

/**
 * 插件卸载云服务
 * 步骤如下：
 * 1、卸载依赖
 * 2、卸载数据
 * 3、卸载插件
 * 4、卸载成功
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginUnInstallAction extends PluginBaseAction
{
    /**
     * 卸载插件
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function start(Request $request)
    {
        // 获取执行步骤
        $step = $request->post('step', 'dependency');
        // 获取插件名称
        $name = $request->post("name");
        // 安装版本
        $version = $request->post("version");
        // 参数验证
        if (empty($name) || empty($version)) {
            throw new \Exception("参数错误");
        }
        // 临时插件包路径
        $package = base_path("runtime/plugin/") . "{$name}-{$version}.zip";
        // 检测临时应用包目录，不存在则创建
        $packageDirPath = dirname($package);
        if (!is_dir($packageDirPath)) {
            mkdir($packageDirPath, 0755, true);
        }
        // 执行转发
        return call_user_func([self::class, $step], $request);
    }

    /**
     * 卸载依赖
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function dependency(Request $request)
    {
        // 获取数据
        $name = $request->post("name");
        $version = $request->post("version");
        // 卸载插件依赖
        PluginDependAction::checkDepend($name, $version);
        // 返回结果
        return self::successFul('依赖卸载完成', [
            'url' => xbUrl('Plugins/uninstall'),
            'query' => [
                'step' => 'database',
            ],
        ]);
    }

    /**
     * 卸载数据
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function database(Request $request)
    {
        // 获取插件名称
        $name = $request->post("name");
        $version = $request->post("version");
        // 卸载插件数据
        parent::installData($name, $version, 'uninstall');
        // 返回结果
        return self::successFul('卸载数据完成', [
            'url' => xbUrl('Plugins/uninstall'),
            'query' => [
                'step' => 'uninstall',
            ],
        ]);
    }

    /**
     * 卸载插件
     * @param \support\Request $request
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function uninstall(Request $request)
    {
        // 获取数据
        $name = $request->post("name");
        $pluginDir       = base_path("plugin/{$name}");
        // 删除插件目录
        remove_dir($pluginDir);
        // 返回结果
        return self::successFul('插件卸载完成', [
            'url' => xbUrl('Plugins/uninstall'),
            'query' => [
                'step' => 'complete',
            ],
        ]);
    }
    
    /**
     * 卸载完成
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function complete(Request $request)
    {
        // 获取插件名称
        $name = $request->post("name");
        $version = $request->post("version");
        parent::installOk($name, $version);
        // 返回结果
        return self::success('插件卸载成功');
    }
}