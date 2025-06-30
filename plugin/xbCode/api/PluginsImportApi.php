<?php
namespace plugin\xbCode\api;

/**
 * 插件安装接口类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginsImportApi extends PluginsBaseApi
{
    /**
     * 安装步骤
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $steps = [
        [
            'title' => '上传插件',
            'name' => 'upload',
            'next' => 'unzip',
        ],
        [
            'title' => '解压插件',
            'name' => 'unzip',
            'next' => 'success',
        ],
    ];
}