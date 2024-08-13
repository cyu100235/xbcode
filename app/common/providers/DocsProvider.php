<?php
namespace app\common\providers;

use Exception;

/**
 * 文档服务提供者
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class DocsProvider
{
    /**
     * 获取插件接口文档
     * @return string[]
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function apps()
    {
        $infos = glob(base_path('plugin/**/info.json'));
        $data  = [];
        foreach ($infos as $path) {
            // 插件名称
            $name = basename(dirname($path));
            // 插件信息
            $info = self::pluginInfo($name);
            if (empty($info)) {
                continue;
            }
            $configPath = base_path("plugin/{$name}/config/apidoc.php");
            if (!file_exists($configPath)) {
                continue;
            }
            $config = require $configPath;
            if (empty($config['apps'])) {
                continue;
            }
            $item = [
                'title' => $info['title'],
                'key'   => $name,
                'items' => $config['apps'],
            ];
            if (empty($config['apps'])) {
                $item['path'] = "plugin\\{$name}\\app\\controller";
            }
            $data[] = $item;
        }
        if (empty($data)) {
            $data = [
                [
                    'title' => '默认应用',
                    'path'  => 'app\\controller',
                    'key'   => 'home'
                ],
            ];
        }
        return $data;
    }

    /**
     * markdown文档
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function docs()
    {
        // 事件文档
        $events = glob(base_path('plugin/**/docs/events.md'));
        $data   = [];
        foreach ($events as $path) {
            // 插件路径
            $pluginPath = dirname($path, 2);
            // 插件名称
            $name = basename($pluginPath);
            // 插件信息
            $info = self::pluginInfo($name);
            if (empty($info['title'])) {
                throw new Exception("Plugin {$name} title not exists");
            }
            if (empty($info['name'])) {
                throw new Exception("Plugin {$name} name not exists");
            }
            if (empty($info['version'])) {
                throw new Exception("Plugin {$name} version not exists");
            }
            $data    = [
                [
                    'title' => "{$info['title']}-事件文档",
                    'path'  => "plugin/{$name}/docs/events",
                ],
            ];
            $apiPath = "{$pluginPath}/docs/apis.md";
            if (file_exists($apiPath)) {
                $data[] = [
                    'title' => "{$info['title']}-接口文档",
                    'path'  => "plugin/{$name}/docs/apis",
                ];
            }
            // 检测是否存在配置文档
            $configPath = base_path("plugin/{$name}/config/apidoc.php");
            if (!file_exists($configPath)) {
                continue;
            }
            $config = require $configPath;
            if (empty($config['docs'])) {
                continue;
            }
            $data = array_merge($data, $config['docs']);
        }
        return $data;
    }

    /**
     * 获取插件信息
     * @param string $name
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function pluginInfo(string $name)
    {
        $infoPath = base_path("plugin/{$name}/info.json");
        if (!file_exists($infoPath)) {
            return [];
        }
        return json_decode(file_get_contents($infoPath), true);
    }
}