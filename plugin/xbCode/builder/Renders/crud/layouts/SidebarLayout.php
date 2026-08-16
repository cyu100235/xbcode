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

use plugin\xbCode\builder\Components\Nav;

/**
 * 侧边栏布局
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait SidebarLayout
{
    /**
     * 导航组件
     * @var Nav
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected Nav $navs;

    /**
     * 当前页面完整地址
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $url;

    /**
     * 选中字段
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $sidebarField = '_nav';

    /**
     * 选中数据
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $sidebarActive = '';

    /**
     * 侧边栏组件
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $sidebars = [];

    /**
     * 使用侧边栏组件
     * @param string $field 选中字段
     * @param string $active 选中数据
     * @return Nav
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function useSidebar(string $field = '_nav', string $active = '')
    {
        $this->sidebarField = $field;
        $this->sidebarActive = $active;
        return $this->navs;
    }

    /**
     * 添加侧边栏项
     * @param string $label
     * @param mixed $value
     * @param string $icon
     * @param array $props
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addSidebar(string $label, mixed $value, string $icon = '', array $props = [])
    {
        $this->sidebars[] = [
            'label' => $label,
            'value' => $value,
            'icon' => $icon,
            ...$props,
        ];
        return $this;
    }

    /**
     * 添加侧边栏
     * @param array $sidebars
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addSidebars(array $sidebars)
    {
        $this->sidebars = $sidebars;
        return $this;
    }

    /**
     * 获取侧边栏组件实例
     * @return Nav
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function getSidebar()
    {
        $url = $this->url;
        $field = $this->sidebarField;
        $active = $this->sidebarActive;
        if (empty($this->sidebars)) {
            return $this->navs;
        }
        // 最终跳转地址
        $path = parse_url($url, PHP_URL_PATH);
        // 地址栏参数转数组
        $queryString = parse_url($url, PHP_URL_QUERY);
        // 解析查询字符串为数组
        $query = [];
        if ($queryString) {
            parse_str($queryString, $query);
        }
        // 当前选中值
        $activeValue = $query[$field] ?? $active;
        unset($query[$field]);
        // 如果有 _act 参数，则删除
        if($query['_act']){
            unset($query['_act']);
        }

        // 将参数重新转为字符串
        $query = http_build_query($query);
        $query = urldecode($query);

        // 处理侧边栏数据
        $processItem = function ($item) use ($path, $field, $activeValue, $query, &$processItem) {
            // 侧边栏项值
            $value = $item['value'] ?? '';
            // 侧边栏项是否选中（不要使用===，因为value可能是字符串）
            $item['active'] = $value == $activeValue ? true : false;
            // 侧边栏项跳转地址
            $to = "{$path}?{$field}={$value}";
            if ($query) {
                $to .= "&{$query}";
            }
            $item['to'] = $to;
            $item['icon'] = $item['icon'] ?? 'fa-regular fa-folder-open';
            if (!empty($item['children'])) {
                $item['children'] = array_map($processItem, $item['children']);
            }
            return $item;
        };
        $links = array_map($processItem, $this->sidebars);

        // 追加至导航
        $this->navs->links($links);
        
        // 返回组件实例
        return $this->navs;
    }
}
