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
 */
class LinkAction extends Button
{
    public string $actionType = 'link';

    /**
     * 设置是否可返回上级
     * @param bool $value
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    /**
     * 设置返回页面地址
     * @param string $path
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function isBack(string $path = '')
    {
        $link = $this->link ?? '';
        if($path){
            $query = str_contains($link, '?') ? '&' : '?';
            $link = "{$link}{$query}_redirect={$path}";
        }
        $this->link = $link;
        return $this;
    }
}
