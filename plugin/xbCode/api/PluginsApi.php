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

use Exception;
use support\Log;
use Webman\Event\Event;
use support\think\Cache;
use plugin\xbCode\app\model\Plugins;
use plugin\xbCode\app\validate\PluginValidate;

/**
 * 插件接口类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class PluginsApi
{
    /**
     * 插件列表缓存键名
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $keyName = 'xb-plugins-list';

    /**
     * 系统插件-禁止操作
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static $systemPlugins = [
        'xbCode' => '核心插件',
        'xbCrontab' => '定时任务插件',
        'xbDeveloper' => '开发辅助插件',
        'xbUpload' => '储存插件',
    ];

    /**
     * 实例化
     * @return PluginsApi
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function make()
    {
        $instance = new static;
        return $instance;
    }

    /**
     * 获取系统插件标识列表
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getSystemPluginNames()
    {
        return array_keys(static::$systemPlugins);
    }

    /**
     * 获取本地插件列表
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getLocalPlugins()
    {
        $dir = base_path() . '/plugin/*/plugins.json';
        $files = glob($dir);
        $data = [];
        foreach ($files as $item) {
            // 获取插件标识
            $name = basename(dirname($item));
            // 插件信息
            $plugin = $this->get($name);
            if (empty($plugin)) {
                continue;
            }
            unset($plugin['path']);
            $data[] = $plugin;
        }
        return $data;
    }

    /**
     * 获取本地插件列表
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getPlugins()
    {
        $data = $this->getLocalPlugins();
        foreach ($data as &$value) {
            // 初始排序
            $value['sort'] = 0;
            // 检测该插件是否已安装
            $where = [
                'name' => $value['name'],
            ];
            $state = Plugins::where($where)->value('state');
            // 是否已安装
            $value['install'] = $state ? '20' : '10';
            if ($value['install'] === '20') {
                $value['sort'] = 1;
            }
            // 是否已启用
            $value['state'] = $state ? $state : '10';
            if ($value['state'] === '20') {
                $value['sort'] = 2;
            }
            // 是否系统插件
            $system = in_array($value['name'], $this->getSystemPluginNames()) ? '20' : '10';
            $value['is_system'] = $system;
            // 是否有配置项
            $hasConfig = empty($this->config($value['name'])) ? '10' : '20';
            $value['has_config'] = $hasConfig;
            // 是否有readme文件
            $hasReadme = file_exists(base_path("/plugin/{$value['name']}/README.md")) ? '20' : '10';
            $value['has_readme'] = $hasReadme;
        }
        // 重新排序
        $data = list_sort_by($data, 'sort');
        // 返回数据
        return $data;
    }

    /**
     * 获取插件列表缓存
     * @param bool $force
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getCache(bool $force = false)
    {
        $data = Cache::get($this->keyName);
        if ($data && !$force) {
            return $data;
        }
        $data = $this->getPlugins();
        Cache::set($this->keyName, $data);
        return $data;
    }

    /**
     * 获取插件列表
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getList(array $where = [])
    {
        $data = $this->getCache();
        if (empty($data)) {
            return [];
        }
        $field = $where['field'] ?? '';
        $value = $where['value'] ?? '20';
        if (empty($field)) {
            return $data;
        }
        // 筛选查询条件
        $data = array_filter($data, function ($item) use ($field, $value) {
            return $item[$field] === $value;
        });
        $data = array_values($data);
        return $data;
    }

    /**
     * 获取已安装插件列表
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getInstallList()
    {
        return $this->getList(['field' => 'install']);
    }

    /**
     * 获取已启用插件列表
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getEnabledList()
    {
        return $this->getList(['field' => 'state']);
    }

    /**
     * 获取插件配置
     * @param string $name 插件标识
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function config(string $name)
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
        $cls = new $class;
        if (!method_exists($cls, 'config')) {
            return [];
        }
        return $cls->config();
    }

    /**
     * 获取插件名称列表
     * @param string $state 插件状态：10-未安装；20-已安装；30-已启用
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function pluginNames(string $state = '10')
    {
        $where = [];
        if ($state === '20') {
            $where = ['field' => 'install'];
        }
        if ($state === '30') {
            $where = ['field' => 'state'];
        }
        $plugins = $this->getList($where);
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
    public function previewUrl(string $name)
    {
        return Url::make('preview.svg', false)
            ->plugin($name)
            ->setDomain()
            ->get();
    }

    /**
     * 检测本地插件是否存在
     * @param mixed $name
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function exists(mixed $name)
    {
        $class = "\\plugin\\{$name}\\api\\Install";
        if (!class_exists($class)) {
            return false;
        }
        return true;
    }

    /**
     * 检测插件是否已安装
     * @param string $name
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function installed(string $name)
    {
        $list = $this->pluginNames('20');
        if (empty($list)) {
            return false;
        }
        if (!in_array($name, $list)) {
            return false;
        }
        return true;
    }

    /**
     * 检测是否启用
     * @param string $name
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function hasEnabled(string $name)
    {
        $list = $this->pluginNames('30');
        if (empty($list)) {
            return false;
        }
        if (!in_array($name, $list)) {
            return false;
        }
        return true;
    }

    /**
     * 插件安装检测并抛出异常
     * @param mixed $name
     * @throws \Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function installedThrow(mixed $name)
    {
        if (!$this->installed($name)) {
            throw new Exception("该插件 {$name} 未安装");
        }
    }

    /**
     * 获取插件信息
     * @param string $name 插件标识
     * @param array $fields 排除字段
     * @throws \Exception
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function get(string $name, array $fields = [])
    {
        try {
            $plugin = $this->getPluginThrow($name);
            if ($fields) {
                $data = [];
                foreach ($plugin as $field => $value) {
                    if (!in_array($field, $fields)) {
                        $data[$field] = $value;
                    }
                }
                return $data;
            }
        } catch (\Throwable $th) {
            if (DebugApi::status()) {
                Log::info($th->getMessage());
            }
            return [];
        }
        // 返回数据
        return $plugin;
    }

    /**
     * 获取插件数据（精简字段）
     * @param string $name
     * @return array
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    public function simple(string $name)
    {
        return $this->get($name, [
            'composer',
            'plugins',
            'plugins_list',
            'composer_list',
            'create_time',
            'create_at',
            'preview',
            'has_readme',
            'plugin_path',
            'preview_path',
        ]);
    }

    /**
     * 获取插件选项
     * @param mixed $callback 回调函数
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function options(?callable $callback = null)
    {
        $data = $this->getEnabledList();
        if (!$callback) {
            $callback = function ($item) {
                $item['label'] = "{$item['title']}（{$item['name']}）";
                $item['value'] = $item['name'];
                return $item;
            };
        }
        $data = array_map($callback, $data);
        return $data;
    }

    /**
     * 验证包文件
     * @param string $path
     * @throws Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function verifyPackage(string $path)
    {
        // 验证文件是否存在
        if (!file_exists($path)) {
            throw new Exception('插件包文件不存在');
        }
        // 验证是否zip文件
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($ext, ['zip'])) {
            throw new Exception('插件包必须是zip格式');
        }
    }

    /**
     * 获取插件包内的插件信息
     * @param string $name 插件标识
     * @param string $path 插件包路径
     * @throws Exception
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    public function getPackageInfo(string $name, string $path)
    {
        // 验证包文件
        $this->verifyPackage($path);
        // 打开压缩包
        $zip = new \ZipArchive;
        if ($zip->open($path) !== true) {
            throw new Exception('插件包打开失败');
        }
        $files = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $files[] = $zip->getNameIndex($i);
        }
        $pluginJsonPath = array_find($files, function ($item) {
            return str_contains($item, 'plugins.json');
        });
        // 检测版本文件是否完整
        if (!$zip->locateName($pluginJsonPath)) {
            throw new Exception('插件包不完整，缺少插件信息文件');
        }
        // 读取插件信息
        $plugin = json_decode($zip->getFromName($pluginJsonPath), true);
        // 关闭压缩包资源
        $zip->close();
        // 重设插件标识
        $plugin['name'] = $name;
        // 插件参数验证
        xbValidate(PluginValidate::class, $plugin);
        // 返回插件数据
        return $plugin;
    }

    /**
     * 修复插件包路径
     * @param string $path
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function fixPackage(string $name, string $path)
    {
        // 验证包文件
        $this->verifyPackage($path);
    }

    /**
     * 获取插件信息并抛出异常
     * @param string $name
     * @throws Exception
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getPluginThrow(string $name)
    {
        $filePath = base_path() . "/plugin/{$name}/plugins.json";
        if (!file_exists($filePath)) {
            throw new Exception("{$name}插件不存在");
        }
        $content = file_get_contents($filePath);
        if (empty($content)) {
            throw new Exception("{$name}插件信息文件内容为空");
        }
        $plugin = json_decode($content, true);
        if (empty($plugin)) {
            throw new Exception("{$name}解析插件信息失败");
        }
        if (empty($plugin['title'])) {
            throw new Exception("{$name}插件名称参数错误");
        }
        if (empty($plugin['version'])) {
            throw new Exception("{$name}插件版本名称参数错误");
        }
        if (empty($plugin['author'])) {
            throw new Exception("{$name}插件作者参数错误");
        }
        if (empty($plugin['desc'])) {
            $plugin['desc'] = '';
        }
        // 设置插件标识
        $plugin['name'] = $name;
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
            throw new Exception("{$name}插件预览图不存在");
        }
        $plugin['preview'] = "/app/{$plugin['name']}/preview.svg";
        $plugin['preview_path'] = $previewPath;
        $plugin['plugin_path'] = $pluginPath;
        // 检测是否有readme文件
        $readmePath = $pluginPath . '/readme.md';
        $readme = '';
        if (file_exists($readmePath)) {
            $plugin['readme'] = file_get_contents($readmePath);
        }
        $readmePath = $pluginPath . '/README.md';
        if (file_exists($readmePath)) {
            $plugin['readme'] = file_get_contents($readmePath);
        }
        $plugin['has_readme'] = $readme ? '20' : '10';
        $plugin['readme'] = $readme;
        // 返回数据
        return $plugin;
    }

    /**
     * 获取插件README文件内容
     * @param string $name
     * @return bool|string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getReadme(string $name)
    {
        $filePath = base_path() . "/plugin/{$name}/readme.md";
        if (!file_exists($filePath)) {
            $filePath = base_path() . "/plugin/{$name}/README.md";
        }
        if (!file_exists($filePath)) {
            return '';
        }
        return file_get_contents($filePath);
    }

    /**
     * 设置插件状态
     * @param string $name 插件标识
     * @param string $value 状态值，10未启用，20已启用
     * @throws Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function state(string $name, string $value)
    {
        $model = Plugins::where('name', $name)->find();
        if (!$model) {
            throw new Exception("插件 {$name} 不存在");
        }
        if (!in_array($value, ['10', '20'])) {
            throw new Exception("插件 {$name} 状态值错误");
        }
        $model->state = $value;
        if (!$model->save()) {
            throw new Exception("插件 {$name} 状态设置失败");
        }
        // 刷新缓存
        $this->getCache(true);
        // 触发插件状态事件
        Event::dispatch('xbCode.Plugins.state', [
            'name' => $name,
            'value' => $value,
        ]);
    }

    /**
     * 安装插件记录
     * @param string $name 插件标识
     * @param array $data 插件数据
     * @param string $state 状态值，10未启用，20已启用
     * @throws \Exception
     * @return bool
     */
    public function install(string $name, array $data, string $state = '10')
    {
        $model = Plugins::where('name', $name)->find();
        if (!$model) {
            $model = new Plugins;
        }
        if (!in_array($state, ['10', '20'])) {
            throw new Exception("插件 {$name} 状态值错误");
        }
        return $model->save([
            'name' => $name,
            'title' => $data['title'] ?? '积木云插件',
            'version' => $data['version'] ?? '1.0.0',
            'author' => $data['author'] ?? '积木云',
            'desc' => $data['desc'] ?? '',
            'state' => $state,
        ]);
    }

    /**
     * 获取所有插件依赖
     * @return array[]
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getAllPluginDepend()
    {
        // 获取已安装插件
        $data = $this->getInstallList();
        $depends = [];
        foreach ($data as $value) {
            $depend = $value['plugins'] ?? [];
            if (empty($depend)) {
                continue;
            }
            $depends[$value['name']] = array_keys($depend);
        }
        return $depends;
    }

    /**
     * 检测插件是否有子依赖
     * @param string $name
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function hasPluginDepend(string $name)
    {
        $depends = $this->getAllPluginDepend();
        foreach ($depends as $value) {
            if (in_array($name, $value)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 抛出插件依赖异常
     * @param string $name
     * @throws \Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function hasPluginDependThrow(string $name)
    {
        $depends = $this->getAllPluginDepend();
        foreach ($depends as $key => $value) {
            if (in_array($name, $value)) {
                $plugin = $this->get($key);
                throw new Exception("请先卸载插件：{$plugin['title']}（{$key}）");
            }
        }
    }
}