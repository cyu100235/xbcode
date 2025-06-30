<?php
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