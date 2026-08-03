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

use plugin\xbCode\api\AppEntry;
use plugin\xbCode\api\PluginsApi;

/**
 * 首页控制器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class IndexController extends BaseController
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
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function index()
    {
        return $this->adminView();
    }

    /**
     * 站点信息
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function site()
    {
        $builder = AppEntry::make(request()->app);
        $builder->loginData([
            'login_title' => '管理员登录',
        ]);
        // 设置全局组件
        $components = $this->getConfig('components', []);
        $builder->globalComponents($components);
        // 设置扩展图标库
        $icons = $this->getConfig('icons', []);
        $builder->setIconsLinks($icons);
        $data = $builder->get();
        return $this->successRes($data);
    }

    /**
     * 获取工具栏视图
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function toolbar()
    {
        $toolbar = $this->getConfig('toolbar', []);
        $toolbar = list_sort_by($toolbar, 'sort', 'asc');
        $toolbar = array_column($toolbar, 'name');
        return $this->display([
            'toolbar' => $toolbar,
        ]);
    }

    /**
     * 获取工作台远程视图
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function workbench()
    {
        $workbench = $this->getWorkbenchView();
        return $this->display([
            'workbench' => $workbench,
        ]);
    }

    /**
     * 获取工作台列表
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function getWorkbenchView()
    {
        $data = $this->getConfig('workbench', []);
        $length = count($data);
        if ($length > 1) {
            $data = array_filter($data, function ($item) {
                $name = $item['name'] ?? '';
                return $item['sort'] !== -999 && $name !== 'XbAdminWorkbench';
            });
        } else {
            $data = array_filter($data, function ($item) {
                return $item['sort'] === -999 && $item['name'] === 'XbAdminWorkbench';
            });
        }
        $data = array_values($data);
        $data = list_sort_by($data, 'sort', 'asc');
        $data = array_column($data, 'name');
        return $data;
    }

    /**
     * 获取全局配置
     * @param string $name 配置名称
     * @return array
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    private function getConfig(string $name, mixed $default = null)
    {
        $files = glob(base_path('/plugin/*/config/xbcode.php'));
        $data = [];
        foreach ($files as $path) {
            // 获取插件标识
            $pluginName = basename(dirname($path, 2));
            // 检测插件是否启用
            if (!PluginsApi::make()->hasEnabled($pluginName)) {
                continue;
            }
            // 读取配置文件内容
            $content = file_get_contents($path);
            if (empty($content)) {
                continue;
            }
            $config = include $path;
            if (empty($config)) {
                continue;
            }
            $configValue = $config[$name] ?? '';
            if (empty($configValue)) {
                continue;
            }
            $data = array_merge($data, $configValue);
        }
        if (empty($data)) {
            return $default;
        }
        return $data;
    }
}
