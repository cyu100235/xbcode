<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\api;

use Exception;
use plugin\xbAdmin\app\model\Config;
use plugin\xbAdmin\app\model\UploadEngine;
use plugin\xbAdmin\app\validate\EngineValidate;

/**
 * 储存引擎接口
 *
 * 引擎记录存于 xb_upload_engine，引擎参数与当前启用的引擎名存于
 * xb_config 的 upload 分组（active 键），二者共同决定实际使用的储存引擎。
 */
class EngineApi
{
    /**
     * 创建实例
     * @return EngineApi
     */
    public static function make(): EngineApi
    {
        return new static;
    }

    /**
     * 获取引擎列表
     *
     * 按当前启用的引擎名标记每项的 state，供列表「默认使用」开关展示。
     * @return array
     */
    public function getList(): array
    {
        $data = $this->getCache();
        $active = $this->active();
        foreach ($data as &$item) {
            $item['state'] = $active === $item['name'] ? '20' : '10';
        }
        unset($item);
        return $data;
    }

    /**
     * 初始化安装记录
     *
     * 安装时写入默认引擎标识与本地引擎参数，并登记本地储存引擎记录。
     * @param string $engine 默认引擎标识
     * @return void
     */
    public function init(string $engine = ''): void
    {
        Config::saveValues('xbAdmin', 'upload', [
            'active' => $engine,
            'local' => [
                'type' => 'local',
            ],
        ]);
        $this->add([
            'title' => '本地存储',
            'name' => $engine,
            'plugin' => 'xbAdmin',
            'desc' => '存储在本地服务器，无需配置其他参数',
            'prompt' => '本地存储方式不需要配置其他参数',
        ]);
    }

    /**
     * 添加引擎
     * @param array $data 引擎数据
     * @return void
     * @throws Exception
     */
    public function add(array $data): void
    {
        xbValidate(EngineValidate::class, $data, 'add');
        $model = UploadEngine::where('name', $data['name'])->find();
        if (!$model) {
            $model = new UploadEngine;
        }
        if (!$model->save($data)) {
            throw new Exception('保存引擎失败');
        }
    }

    /**
     * 编辑引擎
     * @param string $engine 引擎标识
     * @param array $data 引擎数据
     * @return void
     * @throws Exception
     */
    public function edit(string $engine, array $data): void
    {
        xbValidate(EngineValidate::class, $data, 'edit');
        $model = UploadEngine::where('name', $engine)->find();
        if (!$model) {
            throw new Exception('储存引擎不存在');
        }
        if (!$model->save($data)) {
            throw new Exception('编辑引擎失败');
        }
    }

    /**
     * 获取引擎所有配置
     * @param string|null $adapter 引擎标识，缺省取当前启用的引擎
     * @return array
     */
    public function getConfig(?string $adapter = null): array
    {
        $data = $this->getEngineConfig($adapter);
        foreach ($data['engine'] as &$value) {
            unset($value['title'], $value['name']);
            $value = array_merge($value['config'], [
                'plugin' => $value['plugin'],
            ]);
        }
        unset($value);
        return $data;
    }

    /**
     * 获取引擎配置
     * @param string|null $adapter 引擎标识，缺省取当前启用的引擎
     * @return array 含 default（默认引擎）与 engine（各引擎配置）
     */
    public function getEngineConfig(?string $adapter = null): array
    {
        $engines = UploadEngine::column('title,name,plugin');
        $default = $adapter ?: $this->active();
        $data = [
            'default' => $default,
            'engine' => [],
        ];
        foreach ($engines as $value) {
            $config = $this->engineValues((string) $value['name']);
            $data['engine'][$value['name']] = [
                'title' => $value['title'],
                'name' => $value['name'],
                'plugin' => $value['plugin'],
                'config' => $config ?: [],
            ];
        }
        return $data;
    }

    /**
     * 获取单个引擎配置
     * @param string $adapter 引擎标识
     * @return array
     * @throws Exception
     */
    public function get(string $adapter): array
    {
        $data = $this->getEngineConfig();
        if (!$data) {
            throw new Exception('请先设置储存配置！');
        }
        return $data['engine'][$adapter] ?? [];
    }

    /**
     * 删除引擎
     * @param string $plugin 引擎所属插件标识
     * @param string $engine 引擎标识
     * @return void
     * @throws Exception
     */
    public function del(string $plugin, string $engine): void
    {
        $model = UploadEngine::where(['plugin' => $plugin, 'name' => $engine])->find();
        if (!$model) {
            throw new Exception('储存引擎不存在');
        }
        if (!$model->delete()) {
            throw new Exception('删除引擎失败');
        }
    }

    /**
     * 获取引擎列表
     * @param bool $force 保留参数，本项目未使用缓存
     * @return array
     */
    public function getCache(bool $force = false): array
    {
        return UploadEngine::order('sort asc, id asc')->select()->toArray();
    }

    /**
     * 获取引擎下拉选项
     * @return array
     */
    public function options(): array
    {
        return array_map(function ($item) {
            $item['label'] = $item['title'];
            $item['value'] = $item['name'];
            return $item;
        }, $this->getCache());
    }

    /**
     * 读取当前启用的引擎标识
     * @return string
     */
    public function active(): string
    {
        $values = Config::groupValues('xbAdmin', 'upload');
        return (string) ($values['active'] ?? '');
    }

    /**
     * 读取指定引擎的参数
     * @param string $name 引擎标识
     * @return array
     */
    protected function engineValues(string $name): array
    {
        $values = Config::groupValues('xbAdmin', 'upload');
        $config = $values[$name] ?? [];
        return is_array($config) ? $config : [];
    }
}