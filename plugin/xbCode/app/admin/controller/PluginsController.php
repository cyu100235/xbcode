<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\app\admin\controller;

use Webman\Event\Event;
use plugin\xbCode\api\Url;
use plugin\xbCode\utils\DirUtil;
use plugin\xbCode\api\ConfigApi;
use plugin\xbCode\api\PluginsApi;
use plugin\xbCode\api\PluginsImportApi;
use plugin\xbCode\api\PluginsInstallApi;
use plugin\xbCode\api\PluginsUninstallApi;
use plugin\xbCode\builder\Renders\XbTabForm;

/**
 * 本地插件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class PluginsController extends BaseController
{
    /**
     * 插件列表
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function index()
    {
        $act = request()->get('_act');
        if ($act) {
            $type = request()->get('type', '');
            $installed = $type === 'installed' ? '20' : '10';
            $data = PluginsApi::make()->getList([
                'field' => 'install',
                'value' => $installed
            ]);
            return $this->successRes($data);
        }
        return $this->display();
    }

    /**
     * 导入插件
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function import()
    {
        $step = request()->post('step', '');
        $class = new PluginsImportApi;
        if ($step) {
            $name = request()->post('name', '');
            $version = request()->post('version', '');
            // 执行步骤
            $result = $class->start($name, $version, $step);
            // 刷新插件缓存
            PluginsApi::make()->getCache(true);
            return $this->successRes($result);
        }
        $vars = [
            'steps' => $class->steps(),
        ];
        return $this->display($vars);
    }

    /**
     * 安装插件
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function install()
    {
        $step = request()->post('step', '');
        $class = new PluginsInstallApi;
        if ($step) {
            $name = request()->post('name', '');
            $version = request()->post('version', '');
            // 执行步骤
            $result = $class->start($name, $version, $step);
            // 刷新插件缓存
            PluginsApi::make()->getCache(true);
            return $this->successRes($result);
        }
        $vars = [
            'steps' => $class->steps(),
        ];
        return $this->display($vars);
    }

    /**
     * 卸载插件
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function uninstall()
    {
        $step = request()->post('step', '');
        $class = new PluginsUninstallApi;
        if ($step) {
            $name = request()->post('name', '');
            $version = request()->post('version', '');
            // 执行步骤
            $result = $class->start($name, $version, $step);
            // 刷新插件缓存
            PluginsApi::make()->getCache(true);
            return $this->successRes($result);
        }
        $vars = [
            'steps' => $class->steps(),
        ];
        return $this->display($vars);
    }

    /**
     * 设置插件状态
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function state()
    {
        $name = request()->post('name', '');
        $value = request()->post('value', '');
        $message = $value === '20' ? '启用' : '禁用';
        PluginsApi::make()->state($name, $value);
        return $this->success("插件状态 {$message} 成功");
    }

    /**
     * 获取插件配置
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function config()
    {
        // 插件标识
        $pluginName = request()->get('name', '');
        // 面板地址
        $path = "{$pluginName}/{$pluginName}";
        if (request()->method() == 'POST') {
            $post = request()->post();
            // 保存配置
            ConfigApi::make($path)->set($post);
            // 触发事件
            Event::dispatch('xbCode.Plugins.Config.save', $post);
            // 返回数据
            return $this->success('保存成功');
        }
        // 获取配置数据
        $formData = ConfigApi::make($path)->get();
        // 创建表单视图
        $builder = XbTabForm::make();
        // 获取模板规则
        $template = PluginsApi::make()->config($pluginName);
        foreach ($template as $value) {
            if (empty($value['name'])) {
                throw new \Exception('选项卡标识参数错误');
            }
            if (empty($value['title'])) {
                throw new \Exception('选项卡标题参数错误');
            }
            if (empty($value['body'])) {
                throw new \Exception('选项卡内容参数错误');
            }
            $builder->addTab($value['name'], $value['title'], $value['body']);
        }
        $api = Url::make('config')->query(['name' => $pluginName]);
        $builder->setSaveApi($api);
        $builder->setSaveMethod('POST');
        $builder->setData($formData);
        $vars = [
            'name' => $pluginName,
            'schema' => $builder,
        ];
        return $this->display($vars);
    }

    /**
     * 删除插件代码
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function del()
    {
        $name = request()->post('name', '');
        if (empty($name)) {
            return $this->fail("插件标识参数错误");
        }
        // 获取插件信息
        $plugin = PluginsApi::make()->get($name);
        if (empty($plugin)) {
            return $this->fail("插件标识：{$name}，插件信息不存在");
        }
        $pluginPath = base_path() . "/plugin/{$name}";
        if (!is_dir($pluginPath)) {
            return $this->fail("插件 {$name} 不存在");
        }
        DirUtil::delDir($pluginPath);
        // 刷新插件列表缓存
        PluginsApi::make()->getCache(true);
        Event::dispatch('xbCode.Plugins.del', $plugin);
        return $this->success("删除成功");
    }

    /**
     * 查看插件说明
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function readme()
    {
        $name = request()->get('name', '');
        if (empty($name)) {
            return $this->fail("插件标识参数错误");
        }
        $readme = PluginsApi::make()->getReadme($name);
        return $this->display([
            'readme' => $readme
        ]);
    }

    /**
     * 刷新插件缓存
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function refresh()
    {
        PluginsApi::make()->getCache(true);
        return $this->success('插件缓存刷新成功');
    }
}
