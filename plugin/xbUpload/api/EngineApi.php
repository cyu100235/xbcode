<?php
namespace plugin\xbUpload\api;

use Exception;
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
     * 获取引擎列表
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getList()
    {
        // 选中启用
        $active = ConfigApi::make('upload')->get('active', '');
        return UploadEngine::order('sort asc, id asc')
            ->select()
            ->each(function ($item) use ($active) {
                $item->state = $active === $item->name ? '20' : '10';
            })
            ->toArray();
    }

    /**
     * 初始化安装记录
     * @param string $engine
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function init(string $engine = '')
    {
        // 安装配置项
        ConfigApi::make('upload')->set([
            'active' => $engine
        ]);
        ConfigApi::make('upload')->set([
            'local.type' => 'local'
        ]);
        // 初始化安装储存记录
        EngineApi::add([
            'title' => '本地存储',
            'name' => Install::$engine,
            'plugin' => 'xbUpload',
            'desc' => '存储在本地服务器',
            'prompt' => '本地存储方式不需要配置其他参数',
        ]);
    }

    /**
     * 添加
     * @param array $data
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function add(array $data)
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
    }

    /**
     * 编辑
     * @param string $engine
     * @param array $data
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function edit(string $engine, array $data)
    {
        xbValidate(EngineValidate::class, $data, 'edit');
        $model = UploadEngine::where('name', $engine)->find();
        if (!$model) {
            throw new Exception('云储存引擎不存在');
        }
        if (!$model->save($data)) {
            throw new Exception('编辑引擎失败');
        }
    }

    /**
     * 获取引擎所有配置
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getConfig(string $adapter = null)
    {
        $engines = UploadEngine::column('name,plugin');
        $default = $adapter ?: ConfigApi::make('upload')->get('active', 'local');
        $data = [
            'default' => $default,
            'engine' => [],
        ];
        foreach ($engines as $value) {
            $config = ConfigApi::make('upload')->get($value['name']);
            $data['engine'][$value['name']] = $config ?: [];
            $data['engine'][$value['name']]['plugin'] = $value['plugin'];
        }
        return $data;
    }

    /**
     * 删除
     * @param string $plugin
     * @param string $engine
     * @throws \Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function del(string $plugin, string $engine)
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
    }
}