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

/**
 * 下载行为
 * 通过配置，可以实现下载请求，它其实是 ajax 的一种特例，自动给 api 加上了 "responseType": "blob"
 * Content-Type: application/pdf
 * Content-Disposition: attachment; filename="download.pdf"
 * 如果接口存在跨域，除了常见的 cors header 外，还需要添加以下 header
 * Access-Control-Expose-Headers:  Content-Disposition
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/action
 * @method $this api(string|array $value) 接口地址
 * @method $this block(bool $value) 将按钮宽度调整为其父宽度的选项
 */
class DownloadAction extends AjaxAction
{
    public string $actionType = 'download';
}
