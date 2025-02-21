<?php
namespace plugin\xbCode\app\admin\controller;

use support\Request;
use plugin\xbCode\XbController;
use plugin\xbConfig\api\Config;
use plugin\xbConfig\api\AppsEntry;

/**
 * 首页控制器
 * @copyright 贵州小白基地网络科技有限公司
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
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        $path = $request->path();
        if (!str_ends_with($path, '/')) {
            return redirect("{$path}/");
        }
        return $this->view('public/backend/index', 'html');
    }

    /**
     * 站点信息
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function site(Request $request)
    {
        $config = [];
        // 获取站点配置
        $config    = Config::get('system', '', [], [
            'refresh' => true,
        ]);
        // 获取版权配置
        $copyright = Config::get('copyright', '', []);
        if (!empty($copyright)) {
            $config['login_beian'] = $copyright;
        }
        // 获取配置
        $data = AppsEntry::get($config);
        return $this->successRes($data);
    }

    /**
     * 获取工作台远程视图
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function workbench(Request $request)
    {
        $plugins = glob(base_path() . '/plugin/*/config/workbench.php');
        $workbench = [];
        foreach ($plugins as $plugin) {
            $temp = include $plugin;
            if (!is_array($temp)) {
                continue;
            }
            $workbench = array_merge($workbench, $temp);
        }
        if ($workbench) {
            return $this->successRes($workbench);
        }
        $data = [
            xbUrl('Index/workbenchView'),
        ];
        return $this->successRes($data);
    }

    /**
     * 工作台视图
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function workbenchView(Request $request)
    {
        return $this->view('app/admin/view/workbench');
    }
}
