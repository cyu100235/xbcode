<?php
namespace app\service\action;

use app\utils\JsonUtil;
use support\Request;
use Exception;

/**
 * 插件安装云服务
 * 步骤如下：
 * 1、下载更新包
 * 2、解压更新包
 * 3、安装依赖
 * 4、执行数据安装
 * 5、安装成功
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginInstallAction extends PluginBaseAction
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
        // 获取执行步骤
        $step = $request->post('step', 'download');
        // 获取插件名称
        $name = $request->post("name");
        // 安装版本
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
        // 获取插件名称
        $name = $request->post("name");
        $version = $request->post("version");
        // 下载文件
        parent::downloadFile($name, $version);
        // 返回结果
        return self::successFul('下载插件成功', [
            'url' => xbUrl('Plugins/install'),
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
        // 获取插件名称
        $name = $request->post("name");
        $version = $request->post("version");
        // 解压文件
        parent::unzipFile($name, $version);
        // 返回结果
        return self::successFul('解压插件完成...', [
            'url' => xbUrl('Plugins/install'),
            'query' => [
                'step' => 'depend',
            ],
        ]);
    }

    /**
     * 安装依赖
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function depend(Request $request)
    {
        // 获取插件名称
        $name = $request->post("name");
        $version = $request->post("version");
        parent::installDepend($name, $version, 'install');
        // 返回结果
        return self::successFul('依赖安装完成...', [
            'url' => xbUrl('Plugins/install'),
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
        // 获取插件名称
        $name = $request->post("name");
        $version = $request->post("version");
        parent::installData($name, $version, 'install');
        // 返回结果
        return self::successFul('数据安装完成...', [
            'url' => xbUrl('Plugins/install'),
            'query' => [
                'step' => 'complete',
            ],
        ]);
    }

    /**
     * 安装完成
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function complete(Request $request)
    {
        // 返回结果
        return self::success('插件安装完成');
    }
}