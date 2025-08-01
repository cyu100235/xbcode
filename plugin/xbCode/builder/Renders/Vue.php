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
namespace plugin\xbCode\builder\Renders;

use JsonSerializable;
use plugin\xbCode\builder\Components\Page;
use plugin\xbCode\builder\Components\Custom\Component;

/**
 * Vue渲染器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class Vue implements JsonSerializable
{
    /**
     * 页面实例
     * @var Page
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private Page $page;

    /**
     * Vue渲染组件
     * @var Component
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private Component $component;

    /**
     * 构造函数
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function __construct()
    {
        $this->page = Page::make();
        $this->component = new Component;
    }

    /**
     * 创建Vue组件实例
     * @param string $api
     * @param array $vars
     * @param array $option
     * @return Vue
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function make(string $api, array $vars = [], array $option = []): Vue
    {
        $component = new static;
        $component->useComponent()->url($api, $vars, $option);
        return $component;
    }

    /**
     * 创建Vue视图
     * @param string $content
     * @param array $vars
     * @param array $option
     * @return Vue
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function view(string $content, array $vars = [], array $option = []): Vue
    {
        $component = new static;
        $component->useComponent()->body($content, $vars, $option);
        return $component;
    }

    /**
     * 获取页面实例
     * @return Page
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function usePage(): Page
    {
        return $this->page;
    }

    /**
     * 获取组件实例
     * @return Component
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function useComponent(): Component
    {
        return $this->component;
    }

    /**
     * 渲染表单
     * @return Page
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function create()
    {
        $page = $this->page;
        $page->body([
            $this->component,
        ]);
        // 返回页面组件实例
        return $page;
    }
    
    /**
     * 将当前对象序列化为JSON格式
     * @return Page
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function jsonSerialize():mixed
    {
        return $this->create();
    }
}
