<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\app\admin\controller;

use plugin\xbAdmin\app\BaseController;
use support\Request;
use plugin\xbAdmin\api\SiteEntry;
use plugin\xbAdmin\app\model\AdminRole;

/**
 * 后台入口控制器
 *
 * index/admin 输出积木云 SPA 单页应用入口，
 * site 下发 SPA 启动所需的站点配置，
 * toolbar/workbench 输出 SPA 以运行时编译方式加载的远程视图。
 */
class IndexController extends BaseController
{
    /**
     * 免登录方法，SPA 入口与站点配置在登录页就需要访问
     * @var array
     */
    protected $noLogin = [
        'index',
        'admin',
        'site',
    ];

    /**
     * 免权限校验方法，登录后的任何管理员都需要
     * @var array
     */
    protected $noAuth = [
        'toolbar',
        'workbench',
    ];

    /**
     * 后台首页
     * @return \support\Response
     */
    public function index()
    {
        return $this->adminView();
    }

    /**
     * 后台单页应用入口
     * @return \support\Response
     */
    public function admin()
    {
        return $this->adminView();
    }

    /**
     * 站点配置
     * @return \support\Response
     */
    public function site()
    {
        $data = SiteEntry::make('api')->get();
        return $this->successRes($data);
    }

    /**
     * 顶部工具栏视图
     *
     * 工具栏扩展位固定渲染为链接组，不依赖任何前端全局组件，
     * 配置为空时视图渲染空元素，不影响顶部布局。
     * @param Request $request
     * @return \support\Response
     */
    public function toolbar(Request $request)
    {
        // TODO 工具栏配置暂用静态值，后续接入 config/xbadmin.php 的 toolbar 配置
        $toolbar = [];
        $list = [];
        foreach ($toolbar as $item) {
            if (!is_array($item)) {
                $item = ['title' => (string) $item];
            }
            $title = trim((string) ($item['title'] ?? ''));
            $url = trim((string) ($item['url'] ?? ''));
            if ($title === '' || $url === '') {
                continue;
            }
            $list[] = [
                'title' => $title,
                'url' => $url,
                'target' => (string) ($item['target'] ?? '_blank'),
                'tips' => (string) ($item['tips'] ?? $title),
                'sort' => (int) ($item['sort'] ?? 0),
            ];
        }
        usort($list, function ($a, $b) {
            return $a['sort'] <=> $b['sort'];
        });
        return $this->display([
            'toolbar' => array_values($list),
        ]);
    }

    /**
     * 工作台视图
     * @param Request $request
     * @return \support\Response
     */
    public function workbench(Request $request)
    {
        $site = SiteEntry::make($this->module())->get();
        $user = $this->currentUser();
        $user['role_title'] = (string) AdminRole::where('id', (int) ($user['role_id'] ?? 0))->value('title');
        return $this->display([
            'web_name' => $site['web_name'],
            'web_version' => $site['web_version'],
            'web_url' => $site['web_url'],
            'copyright' => $site['copyright'],
            'user' => $user,
        ]);
    }

    /**
     * 当前模块标识
     * @return string
     */
    protected function module(): string
    {
        return (string) (request()->app ?: 'admin');
    }
}
