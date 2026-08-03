<?php
/**
 * 积木云渲染器
 * @package  XbCode.0
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\api;

use plugin\xbCode\base\BasePlugin;

/**
 * 安装类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class Install extends BasePlugin
{
    /**
     * 安装
     * @param string $version
     * @param mixed $context
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function install(string $version, mixed $context = null)
    {
        parent::install($version, $context);
    }

    /**
     * 更新
     * @param mixed $version
     * @param mixed $context
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function update(string $version, mixed $context = null)
    {
        parent::uninstall($version, $context);
    }

    /**
     * 卸载
     * @param string $version
     * @param mixed $context
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function uninstall(string $version, mixed $context = null)
    {
        parent::uninstall($version, $context);
    }
}