<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
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
    public function __construct(string $url = '')
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
        // 当前所在地址
        $url = $this->toUrl ?? $this->url;
        $url = $this->checkUrl($url, [
            '_redirect',
            '_act',
        ]);
        // 即将跳转地址
        $link = $path ? $path : $this->link;
        $link = $this->checkUrl($link, [], [
            '_redirect' => $url,
        ]);
        // 目标地址添加返回参数
        $toUrl = "{$link}";
        $this->link = $toUrl;
        return $this;
    }
    /**
     * 解析地址并且排除指定参数
     * @param string $url
     * @param array $query
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function checkUrl(string $url, array $query = [], array $append = [])
    {
        $urls = parse_url($url);
        $querys = explode('&', $urls['query'] ?? '');
        $list = [];
        foreach ($querys as $value) {
            $temp = explode('=', $value);
            if (!isset($temp[0]) || !isset($temp[1])) {
                continue;
            }
            if (in_array($temp[0], $query)) {
                continue;
            }
            $list[$temp[0]] = $temp[1];
        }
        if ($append) {
            $list = array_merge($list, $append);
        }
        $queryStr = $list ? '?' . urldecode(http_build_query($list)) : '';
        return "{$urls['path']}{$queryStr}";
    }
}
