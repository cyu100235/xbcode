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
namespace plugin\xbCode\builder\Renders\crud\layouts;

use plugin\xbCode\builder\Components\CRUD;

/**
 * 底部工具栏
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait FooterToolbarLayout
{

    /**
     * 表格实例
     * @var CRUD
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected CRUD $crud;

    /**
     * 底部工具栏组件
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $footerToolbar = [];

    /**
     * 分页组件
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $pagination = [
        'type' => 'pagination',
        'align' => 'right',
        'layout' => 'total,perPage,pager,go',
        'mode' => 'normal',
        'showPerPage' => true,
        'showPageInput' => true,
        'total' => '${total}',
        'perPageAvailable' => [30, 50, 100, 300],
    ];

    /**
     * 底部批量操作按钮
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $bulkActions = [];

    /**
     * 添加批量操作弹窗按钮
     * @param string $title
     * @param string $url
     * @param string $method
     * @param callable|array $config
     * @return \plugin\xbCode\builder\Components\Action\DialogAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addBulkActionDialog(string $title, string $url, string $method = 'GET', callable|array $option = [])
    {
        $url = $this->getBulkActionAPI($url, $method);
        $component = $this->createButtonDialog($title, $url, $option);
        $this->bulkActions[] = $component;
        return $component;
    }

    /**
     * 添加底部下载请求按钮
     * @param string $title
     * @param string $url
     * @param string $method
     * @return \plugin\xbCode\builder\Components\Action\DownloadAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addBulkActionDownload(string $title, string $url, string $method = 'GET')
    {
        $url = $this->getBulkActionAPI($url, $method);
        $component = $this->createButtonDownload($title, $url);
        $this->bulkActions[] = $component;
        return $component;
    }

    /**
     * 添加底部抽屉按钮
     * @param string $title
     * @param string $url
     * @param string $method
     * @param callable|array $config
     * @return \plugin\xbCode\builder\Components\Action\DrawerAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addBulkActionDrawer(string $title, string $url, string $method = 'GET', callable|array $option = [])
    {
        $url = $this->getBulkActionAPI($url, $method);
        $component = $this->createButtonDrawer($title, $url, $option);
        $this->bulkActions[] = $component;
        return $component;
    }

    /**
     * 添加底部确认框按钮
     * @param string $title
     * @param string $url
     * @param string $content
     * @param string $cTitle
     * @param string $method
     * @return \plugin\xbCode\builder\Components\Action\AjaxAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addBulkActionConfirm(string $title, string $url, string $content = '是否确认操作该数据？', string $cTitle = '温馨提示', string $method = 'GET')
    {
        $url = $this->getBulkActionAPI($url, $method);
        $component = $this->createButtonConfirm($title, $url, $content, $cTitle);
        $this->bulkActions[] = $component;
        return $component;
    }

    /**
     * 添加底部链接按钮
     * @param string $title
     * @param string $url
     * @return \plugin\xbCode\builder\Components\Action\LinkAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addBulkActionLink(string $title, string $url)
    {
        $url = $this->getBulkActionAPI($url, '');
        $component = $this->createButtonLink($title, $url);
        $this->bulkActions[] = $component;
        return $component;
    }

    /**
     * 添加底部URL按钮
     * @param string $title
     * @param string $url
     * @param bool $target
     * @return \plugin\xbCode\builder\Components\Action\UrlAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addBulkActionUrl(string $title, string $url, bool $target = true)
    {
        $url = $this->getBulkActionAPI($url, '');
        $component = $this->createButtonUrl($title, $url, $target);
        $this->bulkActions[] = $component;
        return $component;
    }

    /**
     * 获取批量操作API地址
     * @param string $url
     * @param string $method
     * @throws \Exception
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function getBulkActionAPI(string $url, string $method = 'GET')
    {
        $urls = parse_url($url);
        $path = $urls['path'] ?? '';
        $query = $urls['query'] ?? '';
        if (empty($path)) {
            throw new \Exception('请设置正确的右侧操作API地址');
        }
        parse_str($query, $params);
        // 重新组装URL
        $querys = [
            ...$params,
            'ids' => '${ids|raw}',
        ];
        $path = $method ? "{$method}:{$path}" : $path;
        $url = "{$path}?" . urldecode(http_build_query($querys));
        // 返回URL
        return $url;
    }

    /**
     * 获取底部批量操作按钮
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getBulkActions()
    {
        return $this->bulkActions;
    }

    /**
     * 获取底部工具栏组件
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getFooterToolbar()
    {
        // 检测是否已存在批量操作按钮
        $state = array_filter($this->footerToolbar, function ($item) {
            if (is_array($item)) {
                return isset($item['type']) && $item['type'] === 'bulkActions';
            }
            return $item->type === 'bulkActions';
        });
        if (empty($state)) {
            $this->footerToolbar[] = [
                'type' => 'bulkActions',
                'align' => 'left',
            ];
        }
        // 设置分页组件
        $this->footerToolbar[] = $this->pagination;
        // 返回批量操作按钮
        return $this->footerToolbar;
    }
}
