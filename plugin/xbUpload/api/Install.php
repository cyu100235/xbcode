<?php
namespace plugin\xbUpload\api;

use plugin\xbCode\base\BasePlugin;

/**
 * 安装类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class Install extends BasePlugin
{
    /**
     * 存储引擎标识
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static $engine = 'local';

    /**
     * 安装
     * @param string $version
     * @param mixed $context
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function install(string $version = '', mixed $context = null)
    {
        parent::install($version, $context);
        // 安装储存记录
        EngineApi::make()->init(static::$engine);
    }

    /**
     * 更新
     * @param string $version
     * @param mixed $context
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function update(string $version = '', mixed $context = null)
    {
        parent::update($version, $context);
    }

    /**
     * 卸载
     * @param string $version
     * @param mixed $context
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function uninstall(string $version = '', mixed $context = null)
    {
        parent::uninstall($version, $context);
        // 删除储存记录
        EngineApi::make()->del($this->name, static::$engine);
    }
}