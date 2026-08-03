<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\trait;

use Exception;
use plugin\xbCode\api\Url;
use plugin\xbCode\api\ConfigApi;
use plugin\xbCode\api\ConfigView;
use plugin\xbCode\builder\Renders\XbTabForm;

/**
 * 系统配置控制器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait ConfigTrait
{
    /**
     * 普通配置项表单
     * @param string $group
     * @throws Exception
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function configNormal(string $group = '', ?callable $callback = null)
    {
        if (empty($group)) {
            $plugin = request()->plugin;
            $action = request()->action;
            $group = "{$plugin}/{$action}";
        }
        if (!method_exists($this, 'success') || !method_exists($this, 'fail')) {
            throw new Exception('必须在控制器内引入使用');
        }
        if (request()->method() === 'PUT') {
            $post = request()->post();
            // 保存配置
            ConfigApi::make($group)->set($post);
            // 返回数据
            return $this->success('保存成功');
        }
        // 获取配置数据
        $formData = ConfigApi::make($group)->get('', []);
        // 获取普通表单视图
        $builder = ConfigView::getConfigBuilder($group);
        $builder->useForm()->wrapWithPanel(false);
        $builder->setSaveApi(Url::make("Setting/config/{$group}"));
        $builder->setSaveMethod('PUT');
        $builder->setData($formData);
        if ($callback) {
            $callback($builder);
        }
        return $this->successRes($builder);
    }

    /**
     * 获取选项卡配置项表单
     * @param string $group
     * @param mixed $callback
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function configTabs(string $group = '', ?callable $callback = null)
    {
        if (empty($group)) {
            $plugin = request()->plugin;
            $action = request()->action;
            $group = "{$plugin}/{$action}";
        }
        if (!method_exists($this, 'success') || !method_exists($this, 'fail')) {
            throw new Exception('必须在控制器内引入使用');
        }
        if (request()->method() === 'PUT') {
            $_tab = request()->get('_tab');
            $post = request()->post();
            if ($_tab) {
                $post = [
                    $_tab => $post,
                ];
            }
            ConfigApi::make($group)->set($post);
            // 返回数据
            return $this->success('保存成功');
        }
        // 获取配置数据
        $formData = ConfigApi::make($group)->get('', []);
        // 获取普通表单视图
        $builder = ConfigView::getTabsBuilder($group);
        $builder->setSaveApi(Url::make("Setting/config/{$group}"));
        $builder->setSaveMethod('PUT');
        $builder->setData($formData);
        if ($callback) {
            $callback($builder);
        }
        return $this->successRes($builder);
    }

    /**
     * 获取侧边配置项表单
     * @param string $group
     * @param mixed $callback
     * @throws Exception
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function configSidebar(string $group = '', ?callable $callback = null)
    {
        return $this->configTabs($group, function (XbTabForm $builder) use ($callback) {
            $builder->useTabs()->tabsMode('vertical');
            if ($callback) {
                $callback($builder);
            }
        });
    }
}