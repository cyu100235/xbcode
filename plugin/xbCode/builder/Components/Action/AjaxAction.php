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
 * 网络请求行为
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/action
 * @method $this api(string $value) 接口地址
 * @method $this feedback(array $value) 当 ajax 返回正常后，还能接着弹出一个 dialog 做其他交互。返回的数据可用于这个 dialog
 * @method $this reload(string $value) 刷新页面
 * @method $this redirect(string $value) 重定向地址
 * @method $this messages(array $value) 操作成功提示信息(可选)
 */
class AjaxAction extends Button
{
    public string $actionType = 'ajax';
}
