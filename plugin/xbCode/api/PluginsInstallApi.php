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
namespace plugin\xbCode\api;

/**
 * 插件安装接口类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginsInstallApi extends PluginsBaseApi
{
    /**
     * 安装步骤
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $steps = [
        [
            'title' => '预检插件',
            'name' => 'preChecked',
            'next' => 'install',
        ],
        [
            'title' => '安装数据',
            'name' => 'install',
            'next' => 'finish',
        ],
        [
            'title' => '安装完成',
            'name' => 'finish',
            'next' => 'success',
        ],
    ];

    /**
     * 预检插件
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function preChecked()
    {
        // 检查插件字段
        Packages::config($this->name);
        // 检查插件依赖
        Packages::plugins($this->name);
        // 插件路径
        $pluginPath = base_path() . "/plugin/{$this->name}";
        // 检查可读权限
        if (!is_readable($pluginPath)) {
            throw new \Exception("插件目录不可读，请检查权限：{$pluginPath}");
        }
        // 检查可写权限
        if (!is_writable($pluginPath)) {
            throw new \Exception("插件目录不可写，请检查权限：{$pluginPath}");
        }
        return $this->nextResult("预检完成，开始安装依赖...");
    }

    /**
     * 执行安装数据
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function install()
    {
        $this->script();
        return $this->nextResult('安装数据完成...');
    }
}