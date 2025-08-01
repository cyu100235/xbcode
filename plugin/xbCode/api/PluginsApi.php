<?php
namespace plugin\xbCode\api;

use Exception;
use plugin\xbCode\app\model\Plugins;

/**
 * 插件接口类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginsApi
{
    /**
     * 系统插件-禁止操作
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static $systemPlugins = [
        'xbCode',
        'xbUpload',
    ];

    /**
     * 预览图背景色
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private static $bgColor = [
        '#7B64FF',
        '#F44E3B',
        '#FB9E00',
        '#68BC00',
        '#16A5A5',
    ];

    /**
     * 获取本地插件列表
     * @param string $installed 10未安装，20已安装，不传则获取全部
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function list(string $installed = '')
    {
        $data = static::localPlugins();
        foreach ($data as $key => $value) {
            // 检测该插件是否已安装
            $state = Plugins::where('name', $value['name'])->field('state')->find();
            // 是否已安装
            $value['install'] = $state ? '20' : '10';
            // 是否已启用
            $value['state'] = $state ? $state['state'] : '10';
            // 是否系统插件
            $value['is_system'] = in_array($value['name'], static::$systemPlugins) ? '20' : '10';
            // 是否有配置项
            $value['has_config'] = empty(static::config($value['name'])) ? '10' : '20';
            // 重设数据
            $data[$key] = $value;
        }
        // 如果传入了安装状态，则过滤数据
        $data = array_filter($data, function ($item) use ($installed) {
            // 如果未传入安装状态，则返回全部数据
            if(!$installed){
                return true;
            }
            // 如果传入了安装状态，则根据状态过滤数据
            if ($installed === $item['install']) {
                return true;
            }
            return false;
        });
        // 重置数组索引
        $data = array_values($data);
        // 返回数据
        return $data;
    }

    /**
     * 获取插件配置
     * @param string $name
     * @param string $file
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function config(string $name, string $file = 'panel')
    {
        $path = base_path() . "/plugin/{$name}/api/Install.php";
        if (!file_exists($path)) {
            return [];
        }
        include_once $path;
        $class = "\\plugin\\{$name}\\api\\Install";
        if (!class_exists($class)) {
            return [];
        }
        $cls = new $class();
        if (!method_exists($cls, 'config')) {
            return [];
        }
        return $cls->config($file);
    }

    /**
     * 获取本地插件列表
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function localPlugins()
    {
        $dir = base_path() . '/plugin/xb*/plugins.json';
        $files = glob($dir);
        $data = [];
        foreach ($files as $item) {
            // 获取插件标识
            $name = basename(dirname($item));
            // 插件信息
            $plugin = static::get($name);
            unset($plugin['path']);
            $data[] = $plugin;
        }
        return $data;
    }

    /**
     * 获取本地插件名称列表
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function pluginNames()
    {
        $plugins = static::localPlugins();
        $data = array_column($plugins, 'name');
        return $data;
    }

    /**
     * 获取插件预览图
     * @param string $name
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function previewUrl(string $name)
    {
        return Url::make('preview.svg')->plugin($name)->module('')->domain()->get();
    }

    /**
     * 检测插件是否存在
     * @param mixed $name
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function exists(mixed $name)
    {
        $class = "\\plugin\\{$name}\\api\\Install";
        if (!class_exists($class)) {
            return false;
        }
        return true;
    }

    /**
     * 插件安装检测并抛出异常
     * @param mixed $name
     * @throws \Exception
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function existsThrow(mixed $name)
    {
        if (!static::exists($name)) {
            throw new Exception("该插件 {$name} 不存在");
        }
    }

    /**
     * 获取插件信息
     * @param string $name
     * @throws \Exception
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function get(string $name)
    {
        $filePath = base_path() . "/plugin/{$name}/plugins.json";
        if (!file_exists($filePath)) {
            throw new Exception('插件不存在');
        }
        $content = file_get_contents($filePath);
        if (empty($content)) {
            throw new Exception('插件信息文件内容为空');
        }
        $plugin = json_decode($content, true);
        if (empty($plugin)) {
            throw new Exception('解析插件信息失败');
        }
        if (empty($plugin['title'])) {
            throw new Exception('插件名称参数错误');
        }
        if (empty($plugin['name'])) {
            throw new Exception('插件标识参数错误');
        }
        if (empty($plugin['version'])) {
            throw new Exception('插件版本名称参数错误');
        }
        if (empty($plugin['author'])) {
            throw new Exception('插件作者参数错误');
        }
        if (empty($plugin['desc'])) {
            $plugin['desc'] = '--';
        }
        // 解析插件依赖
        $pluginParse = $plugin['plugins'] ?? [];
        $plugin['plugins_list'] = array_values($pluginParse);
        // 解析第三方包依赖
        $composerParse = $plugin['composer'] ?? [];
        $plugin['composer_list'] = array_values($composerParse);
        // 获取文件创建时间
        $createTime = fileatime($filePath);
        // 转换时间格式
        $createAt = date('Y-m-d H:i:s', $createTime);
        // 添加创建时间
        $plugin['create_time'] = $createTime;
        $plugin['create_at'] = $createAt;
        // 获取插件路径
        $pluginPath = dirname($filePath);
        $previewPath = $pluginPath . '/preview.svg';
        if (!file_exists($previewPath)) {
            // 创建预览图
            static::createPreview($plugin);
        }
        $plugin['preview'] = xbUrl("app/{$plugin['name']}/preview.svg", [], [
            'domain' => true,
            'module' => false,
        ]);
        // 返回数据
        return $plugin;
    }

    /**
     * 创建预览图
     * @param string $filePath
     * @param array $plugin
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function createPreview(array $plugin)
    {
        $targetPath = base_path() . "/plugin/{$plugin['name']}/preview.svg";
        if (file_exists($targetPath)) {
            return true;
        }
        $previewTemplatePath = dirname(__DIR__) . '/data/plugin/preview.tpl';
        if (!file_exists($previewTemplatePath)) {
            throw new Exception('预览图片模板不存在');
        }
        $previewContent = file_get_contents($previewTemplatePath);
        if (empty($previewContent)) {
            return false;
        }
        $bgColor = static::getRandBgColor();
        $str1 = [
            '积木云',
            '#FE9200',
        ];
        $str2 = [
            $plugin['title'],
            $bgColor,
        ];
        $previewContent = str_replace($str1, $str2, $previewContent);
        file_put_contents($targetPath, $previewContent);
        return true;
    }

    /**
     * 获取随机背景色
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private static function getRandBgColor()
    {
        return static::$bgColor[array_rand(static::$bgColor)];
    }

    /**
     * 设置插件状态
     * @param string $name
     * @param string $value
     * @throws \Exception
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function state(string $name, string $value)
    {
        $model = Plugins::where('name', $name)->find();
        if (!$model) {
            throw new Exception("插件 {$name} 不存在");
        }
        if (!in_array($value, ['10', '20'])) {
            throw new Exception("插件 {$name} 状态值错误");
        }
        $model->state = $value;
        return $model->save();
    }
}