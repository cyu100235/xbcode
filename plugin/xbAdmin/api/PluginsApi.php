<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbAdmin\api;

/**
 * 插件接口类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class PluginsApi
{
    /**
     * 实例化
     * @return PluginsApi
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function make()
    {
        return new static;
    }

    /**
     * 获取插件下拉选项
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function options(): array
    {
        $options = [];
        foreach (glob(base_path('plugin/*/plugin.json')) ?: [] as $file) {
            $config = json_decode((string) file_get_contents($file), true);
            if (!is_array($config)) {
                continue;
            }
            $name = basename(dirname($file));
            $options[] = [
                'label' => (string) ($config['title'] ?? $name),
                'value' => $name,
            ];
        }
        return $options;
    }

    /**
     * 插件是否已安装
     * @param string $name 插件标识
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function installed(string $name): bool
    {
        if (empty($name)) {
            return false;
        }
        return is_file(base_path("plugin/{$name}/plugin.json"));
    }
}