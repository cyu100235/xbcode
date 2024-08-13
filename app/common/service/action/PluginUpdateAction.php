<?php
namespace app\common\service\action;

use app\common\utils\JsonUtil;
use support\Request;
use Exception;

/**
 * 插件云服务
 * 步骤如下：
 * 1、下载更新包
 * 2、删除插件目录
 * 3、解压更新包
 * 4、更新依赖
 * 5、执行数据安装
 * 6、安装成功
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginUpdateAction extends PluginAction
{
    use JsonUtil;

    /**
     * 安装插件
     * @param string $name
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function start(Request $request)
    {
        // 获取数据
        $step    = $request->post('step', 'download');
        $name    = $request->post("name");
        $version = $request->post("version");
        // 参数验证
        if (empty($name) || empty($version)) {
            throw new Exception("参数错误");
        }
        // 临时插件包路径
        $package = base_path("runtime/plugin/") . "{$name}-{$version}.zip";
        // 检测临时应用包目录，不存在则创建
        $packageDirPath = dirname($package);
        if (!is_dir($packageDirPath)) {
            mkdir($packageDirPath, 0755, true);
        }
        if (!method_exists(self::class, $step)) {
            throw new Exception("安装步骤错误");
        }
        // 执行转发
        return call_user_func([self::class, $step], $request);
    }

    /**
     * 下载插件
     * @param \support\Request $request
     * @throws \Exception
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function download(Request $request)
    {
        $name    = $request->post("name");
        $version = $request->post("version");
        parent::downloadFile($name, $version);
        // 返回结果
        return self::successFul('下载插件成功', [
            'url'   => xbUrl('Plugins/update'),
            'query' => [
                'step' => 'delPluginDir',
            ],
        ]);
    }

    /**
     * 删除插件目录
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function delPluginDir(Request $request)
    {
        // 获取数据
        $name      = $request->post("name");
        $pluginDir = base_path("plugin/{$name}");
        // 删除插件目录
        remove_dir($pluginDir);
        // 返回结果
        return self::successFul('删除插件目录完成', [
            'url'   => xbUrl('Plugins/update'),
            'query' => [
                'step' => 'unzip',
            ],
        ]);
    }

    /**
     * 解压插件
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function unzip(Request $request)
    {
        // 获取数据
        $name    = $request->post("name");
        $version = $request->post("version");
        parent::unzipFile($name, $version);
        // 返回结果
        return self::successFul('解压插件完成', [
            'url'   => xbUrl('Plugins/update'),
            'query' => [
                'step' => 'database',
            ],
        ]);
    }

    /**
     * 插件数据安装
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function database(Request $request)
    {
        // 获取数据
        $name    = $request->post("name");
        $version = $request->post("version");
        parent::installData($name, $version, 'update');
        // 返回结果
        return self::successFul('数据安装完成', [
            'url'   => xbUrl('Plugins/update'),
            'query' => [
                'step' => 'complete',
            ],
        ]);
    }

    /**
     * 更新完成
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function complete(Request $request)
    {
        // 获取插件名称
        $name    = $request->post("name");
        $version = $request->post("version");
        parent::installOk($name, $version);
        // 返回结果
        return self::success('插件更新完成');
    }
}