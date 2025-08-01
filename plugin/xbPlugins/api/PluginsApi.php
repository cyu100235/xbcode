<?php
namespace plugin\xbPlugins\api;

use Exception;
use plugin\xbPlugins\app\model\Plugins;

/**
 * 插件管理接口
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class PluginsApi
{
    /**
     * 安装插件记录
     * @param string $name 插件名称
     * @param string $local 本地导入：10否，20是
     * @param string $state 是否启用：10否，20是
     * @param string $system 冻结操作：10否，20是
     * @throws Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function addRecord(string $name, string $local = '20', string $state = '10', string $system = '10')
    {
        $path = base_path() . "/plugin/{$name}/plugins.json";
        if (!file_exists($path)) {
            throw new Exception('插件信息文件不存在');
        }
        $data = json_decode(file_get_contents($path), true);
        if (empty($data)) {
            throw new Exception('插件信息读取失败');
        }
        if (empty($data['title'])) {
            throw new Exception('插件名称参数错误');
        }
        if (empty($data['name'])) {
            throw new Exception('插件名称参数错误');
        }
        if (empty($data['author'])) {
            throw new Exception('插件作者参数错误');
        }
        if (empty($data['desc'])) {
            throw new Exception('插件描述参数错误');
        }
        if (empty($data['version'])) {
            throw new Exception('插件版本参数错误');
        }
        $model = Plugins::where('name', $data['name'])->find();
        if (!$model) {
            $model = new Plugins;
        }
        $str = ['v', '.'];
        $version = str_replace($str, '', $data['version']);
        $version = intval($version);
        $installed = [
            'title' => $data['title'],
            'name' => $data['name'],
            'author' => $data['author'],
            'desc' => $data['desc'],
            'version_name' => $data['version'],
            'version' => $version,
            'local' => $local ? '20' : '10',
            'state' => $state,
            'is_system' => $system,
        ];
        if (!$model->save($installed)) {
            throw new Exception('添加插件安装记录失败');
        }
    }

    /**
     * 获取本地插件列表
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getLocalPluginList()
    {
        $data = static::getPluginList(['local' => '20'])
            ->each(function ($item) {
                $item['install'] = '20';
                $item['update'] = '10';
                $item['logo'] = PluginsApi::getLocalPluginPreview($item['name']);
                return $item;
            })->toArray();
        return $data;
    }

    /**
     * 获取已安装插件列表
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getInstalledPluginList()
    {
        $data = static::getPluginList(['local' => '10'])->toArray();
        $names = array_column($data, 'name');
        // 检测插件是否有升级
        $data = array_map(function ($item) use ($names) {
            $update = $names[$item['name']] ?? '10';
            $item['install'] = '20';
            $item['update'] = $update;
            $item['logo'] = PluginsApi::getLocalPluginPreview($item['name']);
            return $item;
        }, $data);
        return $data;
    }

    /**
     * 获取插件名称列表
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getInstallPluginNames()
    {
        $data = static::getPluginList()->toArray();
        $names = array_column($data, 'name');
        return $names;
    }

    /**
     * 获取已安装全部插件选项
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function options()
    {
        $data = static::getPluginList(['is_system'=> '10'])->toArray();
        $options = [];
        foreach ($data as $value) {
            $options[] = [
                'value' => $value['name'],
                'label' => $value['title'],
            ];
        }
        return $options;
    }

    /**
     * 获取已启用插件选项
     * @param string $label
     * @param string $value
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function enableOptions(mixed $callback = null)
    {
        $where = [
            'state' => '20',
            'is_system' => '10',
        ];
        $data = static::getPluginList($where)->toArray();
        $options = [];
        foreach ($data as $key => $item) {
            if ($callback) {
                $options[] = $callback($item, $key);
            }else{
                $options[] = [
                    'value' => $item['name'],
                    'label' => $item['title'],
                ];
            }
        }
        return $options;
    }

    /**
     * 获取本地插件预览图
     * @param string $name
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getLocalPluginPreview(string $name)
    {
        $path = base_path() . "/plugin/{$name}/preview.svg";
        if (!file_exists($path)) {
            return '';
        }
        return xbUrl("app/{$name}/preview.svg", [], true, true, false);
    }

    /**
     * 获取插件列表
     * @param array $where
     * @return array|\think\Collection
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private static function getPluginList(array $where = [])
    {
        $data = Plugins::where($where)
            ->order('id desc')
            ->select()
            ->each(function ($item) {
                $item['update'] = '10';
                return $item;
            });
        return $data;
    }
}