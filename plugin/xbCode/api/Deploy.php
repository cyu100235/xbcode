<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\api;

use support\Log;

/**
 * 部署业务处理类
 *
 * 由根目录 deploy.php 在部署成功后，通过远端 PHP 脚本执行一次：
 *   php plugin/xbCode/hooks/deploy-hook.php
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class Deploy
{
    /**
     * 创建实例
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function make()
    {
        return new static;
    }

    /**
     * 执行部署后置业务
     *
     * 扫描所有插件 api/Install.php，存在 deploy 方法的即调用执行。
     * @return array 各插件执行结果，键为插件标识
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function run(): array
    {
        $result = [];
        foreach ($this->getPlugins() as $name) {
            // 插件安装类文件
            $file = base_path() . "/plugin/{$name}/api/Install.php";
            if (!file_exists($file)) {
                continue;
            }
            try {
                // 插件安装类名称
                $class = "\\plugin\\{$name}\\api\\Install";
                if (!class_exists($class)) {
                    continue;
                }
                // 未定义 deploy 方法则跳过
                if (!method_exists($class, 'deploy')) {
                    continue;
                }
                $context = (new $class)->deploy();
                $result[$name] = [
                    'status' => 'success',
                    'context' => $context,
                ];
            } catch (\Throwable $th) {
                $result[$name] = [
                    'status' => 'error',
                    'message' => $th->getMessage(),
                ];
                Log::error("部署执行插件[{$name}]deploy出错：{$th->getMessage()}");
            }
        }
        return $result;
    }

    /**
     * 获取所有插件标识
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getPlugins(): array
    {
        $dirs = glob(base_path() . '/plugin/*', GLOB_ONLYDIR) ?: [];
        return array_map('basename', $dirs);
    }
}
