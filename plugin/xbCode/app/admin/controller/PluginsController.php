<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @version  1.0
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\app\admin\controller;

use support\Request;
use Webman\Event\Event;
use plugin\xbCode\XbController;
use plugin\xbCode\utils\DirUtil;
use plugin\xbCode\api\ConfigApi;
use plugin\xbCode\api\PluginsApi;
use plugin\xbCode\builder\Builder;
use plugin\xbCode\builder\Renders\Form;
use plugin\xbCode\api\PluginsImportApi;
use plugin\xbCode\api\PluginsInstallApi;
use plugin\xbCode\builder\Components\Tab;
use plugin\xbCode\builder\Components\Tabs;
use plugin\xbCode\api\PluginsUninstallApi;

/**
 * 本地插件
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginsController extends XbController
{
    /**
     * 表格
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        $act = $request->get('_act');
        if ($act) {
            $type = $request->get('type', '');
            $installed = $type === 'installed' ? '20' : '';
            $data = PluginsApi::list($installed);
            return $this->successRes($data);
        }
        return $this->successRes(Builder::display());
    }

    /**
     * 导入插件
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function import(Request $request)
    {
        $step = $request->post('step', '');
        $class = new PluginsImportApi;
        if ($step) {
            $name = $request->post('name', '');
            $version = $request->post('version', '');
            // 执行步骤
            $result = $class->start($name, $version, $step);
            return $this->successRes($result);
        }
        $result = Builder::display('', [
            'steps' => $class->steps(),
        ]);
        return $this->successRes($result);
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
        $step = $request->post('step', '');
        $class = new PluginsInstallApi;
        if ($step) {
            $name = $request->post('name', '');
            $version = $request->post('version', '');
            // 执行步骤
            $result = $class->start($name, $version, $step);
            return $this->successRes($result);
        }
        $result = Builder::display('', [
            'steps' => $class->steps(),
        ]);
        return $this->successRes($result);
    }

    /**
     * 卸载插件
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function uninstall(Request $request)
    {
        $step = $request->post('step', '');
        $class = new PluginsUninstallApi;
        if ($step) {
            $name = $request->post('name', '');
            $version = $request->post('version', '');
            // 执行步骤
            $result = $class->start($name, $version, $step);
            return $this->successRes($result);
        }
        $result = Builder::display('', [
            'steps' => $class->steps(),
        ]);
        return $this->successRes($result);
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
        $value = $request->post('value', '');
        $message = $value === '20' ? '启用' : '禁用';
        if (!PluginsApi::state($name, $value)) {
            return $this->fail("{$message}失败");
        }
        // 触发插件状态事件
        Event::dispatch('plugin.state', [
            'name' => $name,
            'value' => $value,
        ]);
        return $this->success("{$message}成功");
    }

    /**
     * 获取插件配置
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function config(Request $request)
    {
        $name = $request->get('name', '');
        $file = 'panel';
        if ($request->isPost()) {
            $post = request()->post();
            // 保存配置
            ConfigApi::set($name,$post);
            // 返回数据
            return $this->success('保存成功');
        }
        // 获取配置项规则
        $config = PluginsApi::config($name, $file);
        // 获取配置数据
        $configData = ConfigApi::get($name, []);
        // 创建表单视图
        $builder = Builder::form(function (Form $builder) use ($config) {
            $tabItem = [];
            foreach ($config as $key => $item) {
                if (!isset($item['title']) || !$item['title']) {
                    $item['title'] = '未知选项';
                }
                // 添加选项卡子项
                $children = [];
                if (isset($item['children']) && is_array($item['children'])) {
                    foreach ($item['children'] as $childItem) {
                        // 首字母转大写
                        $type = ucfirst($childItem['type'] ?? '');
                        // 如果是组件名称，则转换为类名
                        $displayComponent = 'plugin\\xbCode\\builder\\Components\\' . $type;
                        $formComponent = 'plugin\\xbCode\\builder\\Components\\Form\\' . $type;
                        if (class_exists($displayComponent)) {
                            $type = $displayComponent;
                        } else if (class_exists($formComponent)) {
                            $type = $formComponent;
                        }
                        $title = $childItem['title'] ?? '未知选项';
                        /** @var \plugin\xbCode\builder\Components\BaseSchema */
                        $component = new $type;
                        $component->name($childItem['field'] ?? '')
                            ->label($title)
                            ->value($childItem['value'] ?? '')
                            ->placeholder($childItem['type'])
                            ->setVariables($childItem['extra'] ?? []);
                        // 设置组件提示占位符
                        $componentType = $component->getComponentType();
                        if (!$componentType) {
                            $component->placeholder("请填写{$title}");
                        }
                        $children[] = $component;
                    }
                    $children[] = [
                        'type'=> 'submit',
                        'level' => 'primary',
                        'label' => '提交保存',
                        'primary' => true,
                    ];
                }
                /** @var Tab */
                $tab = Tab::make();
                $tabItem[] = $tab->name($key)->title($item['title'])->body($children);
            }
            /** @var Tabs */
            $tabs = Tabs::make();
            $tabs->tabs($tabItem)->getVariables();
            $builder->addRowGroup([$tabs]);
            $builder->addFormButton();
            $builder->useForm()->wrapWithPanel(false);
        });
        $builder->setApi(xbUrl('plugins/config', ['name' => $name]));
        $builder->setMethod('POST');
        $builder->setData($configData);
        $result = Builder::display('', [
            'name' => $name,
            'schema' => $builder,
        ]);
        return $this->successRes($result);
    }

    /**
     * 删除插件代码
     * @param \support\Request $request
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function del(Request $request)
    {
        $name = $request->post('name', '');
        $pluginPath = base_path() . "/plugin/{$name}";
        if (!is_dir($pluginPath)) {
            return $this->fail("插件 {$name} 不存在");
        }
        DirUtil::delDir($pluginPath);
        return $this->success("删除成功");
    }
}
