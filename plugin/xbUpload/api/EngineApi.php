<?php
/**
 * 积木云渲染器
 * @package  XbCode.0
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbUpload\api;

use Exception;
use support\think\Cache;
use plugin\xbCode\api\ConfigApi;
use plugin\xbCode\api\PluginsApi;
use plugin\xbUpload\service\Driver;
use plugin\xbUpload\app\model\UploadEngine;
use plugin\xbUpload\app\validate\EngineValidate;

/**
 * 引擎接口
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class EngineApi
{
    /**
     * 缓存键名
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $keyName = 'xbCode-Engine';

    /**
     * 连接检测缓存键名
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $connectKeyName = 'xbCode-Engine-Connect';

    /**
     * 连接检测缓存有效期（秒）
     * @var int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected int $connectCacheTtl = 600;

    /**
     * 创建实例
     * @return EngineApi
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function make()
    {
        $class = new static;
        return $class;
    }

    /**
     * 获取引擎列表
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getList()
    {
        $data = $this->getCache();
        // 获取当前选中的适配器名称
        $active = ConfigApi::make('upload')->get('active', '');
        // 获取连接检测结果缓存
        $connectCache = $this->getConnectCache();
        foreach ($data as &$item) {
            // 选中启用
            $item['state'] = $active === $item['name'] ? '20' : '10';
            // 是否已配置
            $item['config'] = $this->hasConfig($item['name'], (string) ($item['plugin'] ?? '')) ? '20' : '10';
            // 连接检测结果：20正常，30异常，10未检测
            $result = $connectCache[$item['name']] ?? null;
            $item['connect'] = $result === null ? '10' : ($result['ok'] ? '20' : '30');
            $item['connect_message'] = $result['message'] ?? '';
        }
        return $data;
    }

    /**
     * 检测引擎配置是否已填写
     * @param string $name 引擎标识
     * @param string $plugin 插件标识，已有时可避免重复查询
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function hasConfig(string $name, string $plugin = '')
    {
        if (empty($plugin)) {
            $plugin = (string) UploadEngine::where('name', $name)->value('plugin', '');
            if (empty($plugin)) {
                return false;
            }
        }
        // 本地存储无需填写配置参数
        if ($plugin === 'xbUpload') {
            return true;
        }
        $config = ConfigApi::make('upload')->get($name, []);
        if (empty($config) || !is_array($config)) {
            return false;
        }
        // 仅存在 plugin 字段说明尚未填写任何配置
        $fields = array_diff(array_keys($config), ['plugin']);
        return !empty($fields);
    }

    /**
     * 检测引擎配置参数是否可用且已连通
     * @param string $name 引擎标识
     * @throws Exception
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function check(string $name)
    {
        $model = UploadEngine::where('name', $name)->find();
        if (!$model) {
            throw new Exception('云储存引擎不存在');
        }
        // 检测插件是否启用
        if (!PluginsApi::make()->hasEnabled($model['plugin'])) {
            throw new Exception('插件未启用，请先启用插件');
        }
        // 检测是否已配置
        if (!$this->hasConfig($name)) {
            throw new Exception("请先完成「{$model['title']}」储存设置");
        }
        $ok = false;
        $message = '';
        try {
            $driver = Driver::make($this->getConfig($name), $name);
            $ok = (bool) $driver->check();
            if (!$ok) {
                $message = $driver->getError() ?: '连接失败，请检查配置参数';
            }
        } catch (\Throwable $e) {
            $ok = false;
            $message = $e->getMessage();
        }
        // 写入连接检测缓存
        $this->setConnectCache($name, $ok, $message);
        return [
            'ok' => $ok,
            'message' => $message,
        ];
    }

    /**
     * 获取连接检测结果缓存
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getConnectCache()
    {
        $data = Cache::get($this->connectKeyName);
        return is_array($data) ? $data : [];
    }

    /**
     * 写入连接检测结果缓存
     * @param string $name 引擎标识
     * @param bool $ok 是否连通
     * @param string $message 提示信息
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setConnectCache(string $name, bool $ok, string $message = '')
    {
        $data = $this->getConnectCache();
        $data[$name] = [
            'ok' => $ok,
            'message' => $message,
            'time' => date('Y-m-d H:i:s'),
        ];
        Cache::set($this->connectKeyName, $data, $this->connectCacheTtl);
    }

    /**
     * 清除指定引擎的连接检测缓存
     * @param string $name 引擎标识
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function clearConnectCache(string $name)
    {
        $data = $this->getConnectCache();
        if (isset($data[$name])) {
            unset($data[$name]);
            Cache::set($this->connectKeyName, $data, $this->connectCacheTtl);
        }
    }

    /**
     * 初始化安装记录
     * @param string $engine
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function init(string $engine = '')
    {
        // 安装配置项
        ConfigApi::make('upload')->set([
            'active' => $engine
        ]);
        ConfigApi::make('upload')->set([
            'local' => [
                'type' => 'local'
            ]
        ]);
        // 初始化安装储存记录
        $this->add([
            'title' => '本地存储',
            'name' => $engine,
            'plugin' => 'xbUpload',
            'desc' => '存储在本地服务器，无需配置其他参数',
            'prompt' => '本地存储方式不需要配置其他参数',
        ]);
        $this->getCache(true);
    }

    /**
     * 添加
     * @param array $data
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function add(array $data)
    {
        xbValidate(EngineValidate::class, $data, 'add');
        // 查询数据
        $model = UploadEngine::where('name', $data['name'])->find();
        if (!$model) {
            $model = new UploadEngine;
        }
        if (!$model->save($data)) {
            throw new Exception('保存引擎失败');
        }
        $this->getCache(true);
    }

    /**
     * 编辑
     * @param string $engine
     * @param array $data
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function edit(string $engine, array $data)
    {
        xbValidate(EngineValidate::class, $data, 'edit');
        $model = UploadEngine::where('name', $engine)->find();
        if (!$model) {
            throw new Exception('云储存引擎不存在');
        }
        if (!$model->save($data)) {
            throw new Exception('编辑引擎失败');
        }
        $this->getCache(true);
    }

    /**
     * 获取引擎所有配置
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getConfig(string|null $adapter = null)
    {
        $data = $this->getEngineConfig($adapter);
        foreach ($data['engine'] as &$value) {
            unset($value['title']);
            unset($value['name']);
            $value = array_merge($value['config'], [
                'plugin' => $value['plugin'],
            ]);
        }
        return $data;
    }

    /**
     * 获取引擎配置
     * @param string|null $adapter
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getEngineConfig(string|null $adapter = null)
    {
        $engines = UploadEngine::column('title,name,plugin');
        $default = $adapter ?: ConfigApi::make('upload')->get('active', 'local');
        $data = [
            'default' => $default,
            'engine' => [],
        ];
        foreach ($engines as $value) {
            $config = ConfigApi::make('upload')->get($value['name']);
            $data['engine'][$value['name']]['title'] = $value['title'];
            $data['engine'][$value['name']]['name'] = $value['name'];
            $data['engine'][$value['name']]['plugin'] = $value['plugin'];
            $data['engine'][$value['name']]['config'] = $config ?: [];
        }
        return $data;
    }

    /**
     * 获取引擎配置
     * @param string $adapter
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function get(string $adapter)
    {
        $data = $this->getEngineConfig();
        if (!$data) {
            throw new Exception('请先设置储存配置！');
        }
        $config = $data['engine'][$adapter] ?? [];
        return $config;
    }

    /**
     * 删除
     * @param string $plugin
     * @param string $engine
     * @throws Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function del(string $plugin, string $engine)
    {
        $where = [
            'plugin' => $plugin,
            'name' => $engine,
        ];
        $model = UploadEngine::where($where)->find();
        if (!$model) {
            throw new Exception('云储存引擎不存在');
        }
        if (!$model->delete()) {
            throw new Exception('删除引擎失败');
        }
        $this->getCache(true);
    }

    /**
     * 获取引擎缓存
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
        $data = UploadEngine::order('sort asc, id asc')
            ->select()
            ->toArray();
        if (empty($data)) {
            return [];
        }
        Cache::set($this->keyName, $data);
        return $data;
    }

    /**
     * 获取选项
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function options()
    {
        $data = $this->getCache();
        $data = array_map(function ($item) {
            $item['label'] = $item['title'];
            $item['value'] = $item['name'];
            return $item;
        }, $data);
        return $data;
    }
}