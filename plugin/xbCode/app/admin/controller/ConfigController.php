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

use plugin\xbCode\api\Url;
use plugin\xbCode\trait\ConfigTrait;
use plugin\xbCode\builder\Renders\XbForm;
use plugin\xbCode\builder\Renders\XbTabForm;

/**
 * 通用配置控制器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class ConfigController extends BaseController
{
    use ConfigTrait;

    /**
     * 通用配置
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function config()
    {
        $plugin = request()->route->param('plugin');
        $name = request()->route->param('name');
        $group = "{$plugin}/{$name}";
        return $this->configNormal($group, function (XbForm $builder) use ($name) {
            $builder->setSaveApi(Url::make("admin/Config/{$name}"));
        });
    }

    /**
     * 通用选项卡配置
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function tabs()
    {
        $plugin = request()->route->param('plugin');
        $name = request()->route->param('name');
        $group = "{$plugin}/{$name}";
        return $this->configTabs($group, function (XbTabForm $builder) use ($name) {
            $builder->setSaveApi(Url::make("admin/Tabs/{$name}"));
        });
    }
}