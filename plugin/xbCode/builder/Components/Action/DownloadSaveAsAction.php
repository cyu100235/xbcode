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

/**
 * 保存文件至本地行为
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/action#%E4%BF%9D%E5%AD%98%E5%88%B0%E6%9C%AC%E5%9C%B0
 * @method $this api(string|array $value) 接口地址
 * @method $this fileName(string|array $value) 下载的文件名
 * @method $this block(bool $value) 将按钮宽度调整为其父宽度的选项
 */
class DownloadSaveAsAction extends DownloadAction
{
    public string $type = 'action';
    public string $actionType = 'saveAs';
}
