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

use plugin\xbCode\api\ConfigApi;
use plugin\xbCode\api\Env;
use support\Request;
use plugin\xbCode\api\AppEntry;
use plugin\xbCode\XbController;

/**
 * 首页控制器
 * @copyright 贵州积木云网络网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class IndexController extends XbController
{
    /**
     * 客户端无需登录的方法
     * @var array
     */
    protected $noLogin = [
        'index',
        'site',
    ];

    /**
     * 首页视图
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        return $this->adminView();
    }

    /**
     * 站点信息
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function site(Request $request)
    {
        $builder = AppEntry::make();
        $data = $builder->get();
        return $this->successRes($data);
    }

    /**
     * 获取工具栏视图
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function toolbar(Request $request)
    {
        return $this->getView($request, 'toolbar');
    }

    /**
     * 获取工作台远程视图
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function workbench(Request $request)
    {
        return $this->getView($request, 'workbench');
    }

    /**
     * 获取插件动态视图
     * @param \support\Request $request
     * @param string $type
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getView(Request $request, string $type)
    {
        $data = glob(base_path() . "/plugin/**/config/{$type}.php");
        if (empty($data)) {
            return $this->display();
        }
        $config = current($data);
        if (!file_exists($config)) {
            return $this->display();
        }
        $plugin = basename(dirname($config, 2));
        $workbench = require $config;
        if (empty($workbench[0]) || empty($workbench[1])) {
            return $this->display();
        }
        $class = new $workbench[0];
        $method = $workbench[1];
        if (!method_exists($class, $method)) {
            return $this->display();
        }
        $request->plugin = $plugin;
        return $class->$method($request);
    }
}
