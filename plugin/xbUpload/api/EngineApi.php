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
        foreach ($data as &$item) {
            // 选中启用
            $item['state'] = $active === $item['name'] ? '20' : '10';
        }
        return $data;
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