<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Renders\crud\layouts;

use plugin\xbCode\builder\Components\Action\UrlAction;

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
     * 是否开启下拉按钮
     * @var bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected bool $dropDownButton = false;

    /**
     * 下拉按钮列表
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $dropDownButtons = [];

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
        $url = $this->getRightActionAPI($url, 'get');
        $component = $this->createButtonDialog($title, $url, $option);
        $component->level('link');
        $this->addRightButtons($component);
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
        $component->level('link');
        $this->addRightButtons($component);
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
        $url = $this->getRightActionAPI($url, 'get');
        $component = $this->createButtonDrawer($title, $url, $option);
        $component->level('link');
        $this->addRightButtons($component);
        return $component;
    }

    /**
     * 添加右侧确认框按钮
     * @param string $title 按钮标题
     * @param string $url 按钮URL
     * @param string|array $confirm 确认框配置
     * - title 确认框标题
     * - content 确认框内容
     * @return \plugin\xbCode\builder\Components\Action\AjaxAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRightActionConfirm(string $title, string $url, array $confirm = [], array $option = [])
    {
        $url = $this->getRightActionAPI($url, 'get');
        $confirmTitle = $confirm['title'] ?? '温馨提示';
        $confirmContent = $confirm['content'] ?? '是否确认操作该数据？';
        /** @var \plugin\xbCode\builder\Components\Action\AjaxAction */
        $component = $this->createButtonConfirm($title, $url, $confirmContent, $confirmTitle);
        $component->level('link');
        $component->className('text-danger');
        $component->setVariables($option);
        $this->addRightButtons($component);
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
        $component->level('link');
        $this->addRightButtons($component);
        return $component;
    }

    /**
     * 添加右侧URL按钮
     * @param string $title
     * @param string $url
     * @param bool $target
     * @return UrlAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRightActionUrl(string $title, string $url, bool $target = true)
    {
        // $url = $this->getRightActionAPI($url, '');
        $component = $this->createButtonUrl($title, $url, $target);
        $component->level('link');
        $this->addRightButtons($component);
        return $component;
    }
    
    /**
     * 添加右侧请求后跳转按钮
     * @param string $title 按钮名称
     * @param string $url API地址
     * @param bool $target 是否新标签页打开
     * @param array $option 组件参数
     * @return UrlAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function addRightActionApiUrl(string $title, string $url, bool $target = true, array $option = [])
    {
        /** @var UrlAction */
        $component = $this->createButtonUrl($title, '', $target);
        $component->vAlign('middle');
        $component->level('link');
        $component->actionType('ajax');

        // 从返回数据中获取URL的字段名，默认使用 url
        $apiUrlField = $option['apiUrlField'] ?? 'url';
        // 是否新标签页打开
        $blank = $option['blank'] ?? true;

        // 执行事件 - 先请求API，成功后从返回数据中获取URL并跳转
        $component->onEvent([
            'click' => [
                'actions' => [
                    [
                        'actionType' => 'ajax',
                        'args' => [
                            'api' => $url,
                        ],
                    ],
                    [
                        'actionType' => 'url',
                        'args' => [
                            'url' => '${event.data.' . $apiUrlField . '}',
                            'blank' => $blank,
                        ],
                    ],
                ],
            ],
        ]);
        $this->addRightButtons($component);
        return $component;
    }

    /**
     * 添加右侧下拉按钮
     * @param string $title
     * @param callable $callback
     * @throws \Exception
     * @return \plugin\xbCode\builder\Components\DropdownButton
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRightDropdownButton(string $title, callable $callback)
    {
        /** @var \plugin\xbCode\builder\Components\DropdownButton */
        $component = $this->createButtonDropdown($title);
        $component->level('link');
        // 开启下拉按钮
        $this->dropDownButton = true;
        // 回调函数，用于添加下拉按钮
        $callback($this);
        // 关闭下拉按钮
        $this->dropDownButton = false;
        $component->buttons($this->dropDownButtons);
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
        // 检测是否存在方法
        if (str_contains($path, ':')) {
            $temp = explode(':', $path);
            $method = isset($temp[0]) ? basename(strtoupper($temp[0])) : '';
            // 重置URL
            $path = str_replace("{$method}:", '', $path);
        }
        parse_str($query, $params);
        // 获取主键
        $primaryKey = $this->primaryKey ?? 'id';
        // 重新组装URL
        $data = array_merge($params, $querys);
        if (!str_contains($url, '${')) {
            $data = array_merge($data, [$primaryKey => '${' . $primaryKey . '}']);
        }
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
     * 添加右侧操作按钮
     * @param mixed $component
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function addRightButtons(mixed $component)
    {
        if ($this->dropDownButton) {
            $this->dropDownButtons[] = $component;
        } else {
            $this->actionButtons[] = $component;
        }
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
