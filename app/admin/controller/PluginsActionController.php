<?php

namespace app\admin\controller;

use app\admin\view\PluginConfigView;
use app\common\service\action\PluginUpdateAction;
use app\common\service\CloudSerivce;
use app\common\utils\ZipUtil;
use app\common\XbController;
use Tinywan\Jwt\JwtToken;
use Webman\Event\Event;
use think\facade\Cache;
use support\Request;

/**
 * 插件操作
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginsActionController extends XbController
{
    /**
     * 不需要登录的方法
     * @var array
     */
    protected $noLogin = [
        'download'
    ];

    /**
     * 导入插件
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function import(Request $request)
    {
        $file = $request->file('file');
        Event::dispatch('common.event.PluginImportEvent.import', $file);
        return $this->success('插件导入成功');
    }

    /**
     * 导出插件
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function export(Request $request)
    {
        $uid        = JwtToken::getCurrentId();
        $name       = $request->get('name', '');
        $pluginPath = base_path("plugin/{$name}/");
        if (!is_dir($pluginPath)) {
            return $this->fail('插件不存在');
        }
        $tempPath = base_path("runtime/plugin/");
        if (!is_dir($tempPath)) {
            mkdir($tempPath, 0755, true);
        }
        // 插件信息
        $info     = CloudSerivce::getPluginInfo($name);
        $packPath = "{$tempPath}export-{$info['name']}-{$info['version']}.zip";
        ZipUtil::build($packPath, $pluginPath);
        $key  = md5($packPath . $uid . time());
        $data = [
            'uid' => $uid,
            'title' => $info['title'],
            'name' => $name,
            'version' => $info['version'],
            'path' => $packPath,
        ];
        Cache::set($key, $data, 3600);
        return $this->successRes([
            'redirect' => xbUrl('PluginsAction/download', ['key' => $key]),
        ]);
    }

    /**
     * 下载插件
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function download(Request $request)
    {
        $key  = $request->get('key', '');
        $data = Cache::get($key);
        if (empty($data)) {
            return $this->fail('下载链接已失效');
        }
        if (empty($data['path'])) {
            return $this->fail('下载连接错误');
        }
        if (!file_exists($data['path'])) {
            return $this->fail('下载文件不存在');
        }
        $filename = "{$data['title']} {$data['name']}{$data['version']}.zip";
        $path     = $data['path'];
        return response()->download($path, $filename);
    }

    /**
     * 安装插件
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function install(Request $request)
    {
        $data         = $request->post();
        $data['step'] = $request->post('step', 'depend');
        $result       = Event::dispatch('common.event.PluginInstallEvent.start', $data);
        $data         = current($result);
        return $data;
    }

    /**
     * 更新插件
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    # TODO:更新插件未完成
    public function update(Request $request)
    {
        return PluginUpdateAction::start($request);
    }

    /**
     * 卸载插件
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function uninstall(Request $request)
    {
        $data         = $request->post();
        $data['step'] = $request->post('step', 'database');
        $result       = Event::dispatch('common.event.PluginUnInstallEvent.start', $data);
        $data         = current($result);
        return $data;
    }

    /**
     * 购买订单
     * @param \support\Request $request
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    # TODO:购买订单未完成
    public function order(Request $request)
    {
        return CloudSerivce::create($request);
    }

    /**
     * 统一下订单
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    # TODO:统一下订单未完成
    public function unifiedOrder(Request $request)
    {
        return CloudSerivce::unifiedOrder($request);
    }

    /**
     * 插件详情
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    # TODO:插件详情未完成
    public function detail(Request $request)
    {
        $name    = $request->get('name', '');
        $version = $request->get('version', '');
        $data    = CloudSerivce::pluginDetail($name, $version);
        return $this->successRes($data);
    }

    /**
     * 插件配置
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function config(Request $request)
    {
        if ($request->method() === 'PUT') {
            $group  = $request->get('name', '');
            $post   = $request->post();
            $active = $post['active'];
            unset($post['active']);
            $data = [
                'group' => "{$group}_{$active}",
                'data' => $post
            ];
            Event::dispatch('common.event.SettingsEvent.config', $data);
            // 返回结果
            return $this->success('保存成功');
        }
        $builder = PluginConfigView::config();
        $builder->setMethod('PUT');
        $data = $builder->create();
        return $this->successRes($data);
    }

    /**
     * 插件演示
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    # TODO:插件演示未完成
    public function demo(Request $request)
    {
        $data = [];
        return $this->successRes($data);
    }
}
