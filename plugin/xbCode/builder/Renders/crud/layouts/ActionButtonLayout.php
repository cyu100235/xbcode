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

/**
 * 表格操作按钮列表
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait ActionButtonLayout
{
    /**
     * 主键键名
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $primaryKey = 'id';

    /**
     * 操作选项配置
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $actionConfig = [
        'label' => '操作',
        'width' => 'auto',
    ];

    /**
     * 获取表格操作按钮列表
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $actionButtons = [];

    /**
     * 添加右侧操作弹窗按钮
     * @param string $title
     * @param string $url
     * @param callable|array $option
     * @return \plugin\xbCode\builder\Components\Action\DialogAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRightActionDialog(string $title, string $url, callable|array $option = [])
    {
        $url = $this->getRightActionAPI($url, 'get', ['_dialog' => 1]);
        $component = $this->createButtonDialog($title, $url, $option);
        $this->actionButtons[] = $component;
        return $component;
    }

    /**
     * 添加右侧下载请求按钮
     * @param string $title
     * @param string $url
     * @return \plugin\xbCode\builder\Components\Action\DownloadAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRightActionDownload(string $title, string $url)
    {
        $url = $this->getRightActionAPI($url);
        $component = $this->createButtonDownload($title, $url);
        $this->actionButtons[] = $component;
        return $component;
    }

    /**
     * 添加右侧抽屉按钮
     * @param string $title
     * @param string $url
     * @param callable|array $option
     * @return \plugin\xbCode\builder\Components\Action\DrawerAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRightActionDrawer(string $title, string $url, callable|array $option = [])
    {
        $url = $this->getRightActionAPI($url, 'get', ['_dialog' => 1]);
        $component = $this->createButtonDrawer($title, $url, $option);
        $this->actionButtons[] = $component;
        return $component;
    }

    /**
     * 添加右侧确认框按钮
     * @param string $title
     * @param string $url
     * @param string $content
     * @param string $cTitle
     * @return \plugin\xbCode\builder\Components\Action\AjaxAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRightActionConfirm(string $title, string $url, string $content = '是否确认操作该数据？', string $cTitle = '温馨提示')
    {
        $url = $this->getRightActionAPI($url, 'get', ['_dialog' => 1]);
        $component = $this->createButtonConfirm($title, $url, $content, $cTitle);
        $this->actionButtons[] = $component;
        return $component;
    }

    /**
     * 添加右侧链接按钮
     * @param string $title
     * @param string $url
     * @return \plugin\xbCode\builder\Components\Action\LinkAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRightActionLink(string $title, string $url)
    {
        $url = $this->getRightActionAPI($url, '');
        $component = $this->createButtonLink($title, $url);
        $this->actionButtons[] = $component;
        return $component;
    }

    /**
     * 添加右侧URL按钮
     * @param string $title
     * @param string $url
     * @param bool $target
     * @return \plugin\xbCode\builder\Components\Action\UrlAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRightActionUrl(string $title, string $url, bool $target = true)
    {
        $url = $this->getRightActionAPI($url, '');
        $component = $this->createButtonUrl($title, $url, $target);
        $this->actionButtons[] = $component;
        return $component;
    }

    /**
     * 获取右侧操作API地址
     * @param string $url
     * @param string $method
     * @param array $querys
     * @throws \Exception
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getRightActionAPI(string $url, string $method = 'get', array $querys = [])
    {
        $urls = parse_url($url);
        $path = $urls['path'] ?? '';
        $query = $urls['query'] ?? '';
        if (empty($path)) {
            throw new \Exception('请设置正确的右侧操作API地址');
        }
        parse_str($query, $params);
        // 获取主键
        $primaryKey = $this->primaryKey ?? 'id';
        // 重新组装URL
        $data = [
            ...$params,
            ...$querys,
            $primaryKey => '${' . $primaryKey . '}',
        ];
        $path = $method ? "{$method}:{$path}" : $path;
        $url = "{$path}?" . urldecode(http_build_query($data));
        return $url;
    }

    /**
     * 设置表格操作按钮配置
     * @param string $name
     * @param mixed $value
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setActionConfig(string $name, mixed $value)
    {
        $this->actionConfig[$name] = $value;
        return $this;
    }

    /**
     * 获取表格操作按钮配置
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getActionConfig()
    {
        return $this->actionConfig;
    }

    /**
     * 获取表格右侧操作按钮列表
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getActionButtons()
    {
        return $this->actionButtons;
    }
}
