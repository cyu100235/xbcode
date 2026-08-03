<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @version  1.0.0
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\app\validate;

use taoser\Validate;

/**
 * 插件验证器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class PluginValidate extends Validate
{
    protected $rule = [
        'title' => 'require|length:2,20',
        'name' => 'require|length:2,20',
        'version' => 'require|length:2,10',
        'author' => 'require|length:2,20',
        'desc' => 'require|length:5,100',
    ];

    protected $message = [
        'title.require' => '插件名称参数错误',
        'title.length' => '插件名称字符长度在2到20之间',
        'name.require' => '插件标识参数错误',
        'name.length' => '插件标识字符长度在2到20之间',
        'version.require' => '版本名称参数错误',
        'version.length' => '版本名称字符长度在2到10之间',
        'author.require' => '插件作者参数错误',
        'author.length' => '插件作者字符长度在2到20之间',
        'desc.require' => '插件描述参数错误',
        'desc.length' => '插件描述字符长度在5到50之间',
    ];

    /**
     * 验证插件包数据
     * @return PluginValidate
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function sceneVerify()
    {
        return $this->only(['title', 'version', 'author', 'desc']);
    }
}
