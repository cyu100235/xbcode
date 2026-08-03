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

use plugin\xbCode\api\ConfigApi;
use plugin\xbCode\builder\Builder;
use plugin\xbCode\trait\ConfigTrait;
use plugin\xbCode\builder\Renders\XbTabForm;

/**
 * 系统配置控制器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class SettingController extends BaseController
{
    use ConfigTrait;

    /**
     * 系统配置
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function system()
    {
        if (empty($group)) {
            $plugin = request()->plugin;
            $action = request()->action;
            $group = "{$plugin}/{$action}";
        }
        if (request()->method() === 'PUT') {
            $post = request()->post();
            if (empty($post)) {
                return $this->fail('配置数据参数错误');
            }
            $this->configTabs($group);
            return $this->success('配置保存成功');
        }
        // 获取配置数据
        $formData = ConfigApi::make($group)->get('', []);
        $builder = $this->getBuilder();
        $builder->setSaveMethod('PUT');
        $builder->setData($formData);
        return $this->successRes($builder);
    }

    /**
     * 获取渲染器
     * @return XbTabForm
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function getBuilder()
    {
        return Builder::tabForm(function (XbTabForm $builder) {
            $template = $this->getTemplates();
            foreach ($template as $value) {
                if (empty($value['name'])) {
                    continue;
                }
                if (empty($value['title'])) {
                    continue;
                }
                if (empty($value['body'])) {
                    continue;
                }
                $builder->addTab($value['name'], $value['title'], $value['body']);
            }
        });
    }

    /**
     * 获取系统配置模板文件
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function getTemplates()
    {
        $files = glob(base_path('plugin/*/config/xbcode.php'));
        $data = [];
        foreach ($files as $file) {
            $name = basename(dirname($file, 2));
            if (!file_exists($file)) {
                continue;
            }
            $fileName = config("plugin.{$name}.xbcode.setting");
            if (empty($fileName)) {
                continue;
            }
            $path = base_path("plugin/{$name}/setting/tabs/{$fileName}.php");
            if (!file_exists($path)) {
                continue;
            }
            $tabs = include $path;
            if (empty($tabs)) {
                continue;
            }
            $tabs = array_map(function ($tab) {
                $tab['sort'] = $tab['sort'] ?? 100;
                return $tab;
            }, $tabs);
            $data = array_merge($data, $tabs);
        }
        $data = list_sort_by($data, 'sort', 'asc');
        return $data;
    }

    /**
     * 版权信息
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function webicp()
    {
        return $this->configNormal();
    }
}