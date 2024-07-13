<?php
namespace app\common\event;

use app\common\service\CloudSerivce;
use app\common\utils\JsonUtil;
use app\model\Plugins;
use Exception;

/**
 * 插件安装
 * 步骤如下：
 * 1、下载更新包
 * 2、解压更新包
 * 3、安装依赖
 * 4、执行数据安装
 * 5、安装成功
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginInstallEvent
{
    use JsonUtil;

    /**
     * 安装插件
     * @param string $name
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function start(array $post)
    {
        // 参数验证
        if (empty($post['name']) || empty($post['version']) || empty($post['step'])) {
            throw new Exception("插件安装参数错误");
        }
        // 临时插件包路径
        $package = base_path("runtime/plugin/") . "{$post['name']}-{$post['version']}.zip";
        // 检测临时应用包目录，不存在则创建
        $packageDirPath = dirname($package);
        if (!is_dir($packageDirPath)) {
            mkdir($packageDirPath, 0755, true);
        }
        if (!method_exists($this, $post['step'])) {
            throw new Exception("插件安装步骤不存在");
        }
        // 执行转发
        return call_user_func([$this, $post['step']], $post);
    }

    /**
     * 安装依赖
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function depend(array $post)
    {
        // 执行依赖安装
        CloudSerivce::installDepend($post['name'], $post['version'], 'install');
        // 返回结果
        return $this->successFul('安装依赖完成...', [
            'url' => xbUrl('PluginsAction/install'),
            'query' => [
                'step' => 'database',
            ],
        ]);
    }

    /**
     * 数据安装
     * @param array $post
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function database(array $post)
    {
        // 执行数据安装
        CloudSerivce::installData($post['name'], $post['version'], 'install');
        // 返回结果
        return $this->successFul('安装数据完成...', [
            'url' => xbUrl('PluginsAction/install'),
            'query' => [
                'step' => 'complete',
            ],
        ]);
    }

    /**
     * 安装完成
     * @param array $post
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function complete(array $post)
    {
        // 更新安装状态
        Plugins::where('name', $post['name'])->save([
            'state' => '20',
        ]);
        // 返回结果
        return $this->success('插件安装完成');
    }
}
