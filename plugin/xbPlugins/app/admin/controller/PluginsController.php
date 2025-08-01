<?php
namespace plugin\xbPlugins\app\admin\controller;

use Exception;
use support\Request;
use plugin\xbCode\XbController;
use plugin\xbUpload\api\UploadApi;
use plugin\xbPlugins\api\PluginsState;
use plugin\xbPlugins\api\PluginsInstall;
use plugin\xbPlugins\api\PluginsUnInstall;
use plugin\xbPlugins\app\validate\PluginValidate;

/**
 * 插件管理
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class PluginsController extends XbController
{
    /**
     * 导入插件
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function import(Request $request)
    {
        if ($request->isGet()) {
            return $this->display();
        }
        $step = $request->post('step', '');
        $name = $request->post('name', '');
        $versionName = $request->post('version', '');
        try {
            if (empty($step)) {
                return $this->uploadPlugin();
            }
            if (empty($name)) {
                throw new Exception('插件标识参数错误');
            }
            if (empty($versionName)) {
                throw new Exception('插件版本参数错误');
            }
            $version = str_replace(['.', 'v'], '', $versionName);
            $class = new PluginsInstall;
            if (!method_exists($class, $step)) {
                throw new Exception('操作步骤参数不存在');
            }
            // 执行安装步骤
            $class->start($step, $name, $versionName, $version, true);
            $installed = [
                'unzip' => '执行解压插件包完成',
                'script' => '执行安装脚本完成',
                'complete' => '恭喜您，插件安装完成',
            ];
            $nexted = [
                'unzip' => 'script',
                'script' => 'complete',
                'complete' => '',
            ];
            if (!isset($nexted[$step])) {
                return $this->successRes([
                    'text' => "执行出错，未知的安装步骤",
                ]);
            }
            if (!isset($installed[$step])) {
                return $this->successRes(['text' => '未知安装步骤']);
            }
            $stepText = $installed[$step];
            $nextedStep = $nexted[$step];
            // 返回成功
            return $this->successRes([
                'text' => "{$stepText}...",
                'next' => $nextedStep,
            ]);
        } catch (\Throwable $th) {
            return $this->failFul($th->getMessage(),403);
        }
    }

    /**
     * 上传插件包并验证
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function uploadPlugin()
    {
        if (!class_exists('ZipArchive')) {
            throw new Exception('未安装ZipArchive扩展');
        }
        // 获取上传文件
        $file = request()->file('file');
        if (empty($file)) {
            throw new Exception('上传文件不存在');
        }
        // 获取文件路径
        $path = $file->getRealPath();
        // 打开压缩包
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            throw new Exception('插件包打开失败');
        }
        // 检测版本文件是否完整
        if (!$zip->getFromName('plugins.json')) {
            throw new Exception('插件包不完整，缺少信息文件');
        }
        if (!$zip->getFromName('preview.svg')) {
            throw new Exception('插件包不完整，缺少预览图');
        }
        // 读取插件信息
        $plugin = json_decode($zip->getFromName('plugins.json'), true);
        // 插件参数验证
        xbValidate(PluginValidate::class, $plugin);
        // 检测插件是否已经安装
        if (PluginsState::checked($plugin['name'])) {
            throw new Exception('该插件已经安装');
        }
        // 关闭压缩包资源
        $zip->close();
        // 上传插件包
        $saveDirPath = runtime_path() . "/plugin";
        $fileName = "{$plugin['name']}-{$plugin['version']}.zip";
        $data = UploadApi::uploadLocal($saveDirPath, $fileName);
        if (empty($data['uri'])) {
            throw new Exception('上传失败');
        }
        // 返回插件信息
        return $this->successRes([
            'text' => "「{$plugin['title']}」上传成功，标识：{$plugin['name']}，作者：{$plugin['author']}",
            'plugin' => [
                'title' => $plugin['title'],
                'name' => $plugin['name'],
                'version' => $plugin['version'],
            ],
        ]);
    }

    /**
     * 购买插件
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function buy(Request $request)
    {
        if ($request->isGet()) {
            return $this->display();
        }
        $data = [];
        return $this->successRes($data);
    }

    /**
     * 安装插件
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function install(Request $request)
    {
        if ($request->isGet()) {
            return $this->display();
        }
        $data = [];
        return $this->successRes($data);
    }

    /**
     * 更新插件
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function update(Request $request)
    {
        if ($request->isGet()) {
            return $this->display();
        }
        $data = [];
        return $this->successRes($data);
    }

    /**
     * 卸载插件
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function uninstall(Request $request)
    {
        if ($request->isGet()) {
            return $this->display();
        }
        $step = $request->post('step', '');
        $name = $request->post('name', '');
        $versionName = $request->post('version_name', '');
        $version = $request->post('version', '');
        if (empty($step)) {
            return $this->fail('卸载步骤参数错误');
        }
        if (empty($name)) {
            return $this->fail('插件标识参数错误');
        }
        if (empty($versionName)) {
            return $this->fail('插件版本名称参数错误');
        }
        if (empty($version)) {
            return $this->fail('插件版本号参数错误');
        }
        $class = new PluginsUnInstall;
        if (!method_exists($class, $step)) {
            return $this->fail('卸载步骤不存在');
        }
        $steps = [
            'script' => '卸载插件数据完成...',
            'delCode' => '卸载插件代码完成...',
            'complete' => '插件卸载完成...',
        ];
        $stepText = $steps[$step] ?? '未知步骤';
        $nexted = [
            'script' => 'delCode',
            'delCode' => 'complete',
            'complete' => '',
        ];
        $nextedStep = $nexted[$step] ?? '';
        $class->start($step, $name, $versionName, $version);
        return $this->successFul($stepText, [
            'next' => $nextedStep,
        ]);
    }

    /**
     * 设置插件状态
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function state(Request $request)
    {
        $name = $request->post('name', '');
        $state = $request->post('state', '');
        if (empty($name)) {
            return $this->fail('插件标识参数错误');
        }
        if (empty($state)) {
            return $this->fail('插件状态参数错误');
        }
        if (!in_array($state, ['10', '20'])) {
            return $this->fail('未知的插件状态');
        }
        // 修改插件状态
        PluginsState::setState($name, $state);
        // 插件状态
        $message = $state == '20' ? '启用' : '禁用';
        // 返回成功
        return $this->success("插件{$message}完成");
    }
}
