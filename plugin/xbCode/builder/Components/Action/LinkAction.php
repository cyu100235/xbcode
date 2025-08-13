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
namespace plugin\xbCode\builder\Components\Action;

use plugin\xbCode\builder\Components\Button;

/**
 * 单页跳转
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/action
 * @method $this link(string $value) 跟 url 不同的是，这是单页跳转方式，不会渲染浏览器，请指定 amis 平台内的页面。可用 ${xxx} 取值
 * @method $this block(bool $value) 将按钮宽度调整为其父宽度的选项
 * @method $this level(string $value) 按钮样式
 * - `link` - 链接样式
 * - `primary` - 主要按钮样式
 * - `enhance` - 增强按钮样式
 * - `secondary` - 次要按钮样式
 * - `info` - 信息按钮样式
 * - `success` - 成功按钮样式
 * - `warning` - 警告按钮样式
 * - `danger` - 危险按钮样式
 * - `light` - 浅色按钮样式
 * - `dark` - 深色按钮样式
 * - `default` - 默认按钮样式
 */
class LinkAction extends Button
{
    public string $actionType = 'link';

    /**
     * 跳转地址
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public string $link;

    /**
     * 所在地址
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $toUrl;

    /**
     * 构造函数
     * @param string $url
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function __construct(string $url)
    {
        $this->toUrl = $url;
    }

    /**
     * 设置返回页面地址
     * @param string $path
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function isBack(string $path = '')
    {
        // 跳转地址
        $link = $this->link ?? '';
        if ($path) {
            $condition = str_contains($link, '?') ? '&' : '?';
            $link = "{$path}{$condition}_redirect={$this->url}";
        } else {
            $url = strtok($this->toUrl, '?');
            $condition = str_contains($link, '?') ? '&' : '?';
            $link = "{$link}{$condition}_redirect={$url}";
        }
        $this->link = $link;
        return $this;
    }
}
