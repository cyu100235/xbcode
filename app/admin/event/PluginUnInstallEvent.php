<?php
namespace app\admin\event;

use app\common\service\CloudSerivce;
use app\common\utils\JsonUtil;
use app\model\Plugins;
use Exception;

/**
 * 插件卸载事件
 * 1、卸载数据
 * 2、卸载插件
 * 3、卸载成功
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginUnInstallEvent
{
    use JsonUtil;

    /**
     * 卸载插件
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function start(array $post)
    {
        // 参数验证
        if (empty($post['name']) || empty($post['version']) || empty($post['step'])) {
            throw new Exception("卸载参数错误");
        }
        // 临时插件包路径
        $package = base_path("runtime/plugin/") . "{$post['name']}-{$post['version']}.zip";
        // 检测临时应用包目录，不存在则创建
        $packageDirPath = dirname($package);
        if (!is_dir($packageDirPath)) {
            mkdir($packageDirPath, 0755, true);
        }
        if (!method_exists($this, $post['step'])) {
            throw new Exception("卸载步骤不存在");
        }
        // 执行转发
        return call_user_func([self::class, $post['step']], $post);
    }

    /**
     * 卸载数据
     * @param array $post
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function database(array $post)
    {
        // 卸载插件数据
        CloudSerivce::installData($post['name'], $post['version'], 'uninstall');
        // 返回结果
        return self::successFul('卸载数据完成', [
            'url' => xbUrl('PluginsAction/uninstall'),
            'query' => [
                'step' => 'uninstall',
            ],
        ]);
    }

    /**
     * 卸载目录
     * @param array $post
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function uninstall(array $post)
    {
        // 获取数据
        $pluginDir = base_path("plugin/{$post['name']}");
        // 删除插件目录
        remove_dir($pluginDir);
        // 返回结果
        return $this->successFul('插件卸载完成', [
            'url' => xbUrl('PluginsAction/uninstall'),
            'query' => [
                'step' => 'complete',
            ],
        ]);
    }

    /**
     * 卸载完成
     * @param array $post
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function complete(array $post)
    {
        $model = Plugins::where('name', $post['name'])->find();
        if ($model) {
            $model->delete();
        }
        // 返回结果
        return self::success('插件卸载成功');
    }
}
