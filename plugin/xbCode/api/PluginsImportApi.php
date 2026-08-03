<?php
namespace plugin\xbCode\api;

use Webman\Event\Event;

/**
 * 插件安装接口类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
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

    /**
     * 上传插件
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function upload()
    {
        $result = parent::upload();
        // 触发事件
        Event::dispatch('xbCode.Plugins.Import.upload', [
            'name' => $this->name,
            'version' => $this->version
        ]);
        return $result;
    }

    /**
     * 解压插件
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function unzip()
    {
        $result = parent::unzip();
        // 触发事件
        Event::dispatch('xbCode.Plugins.Import.unzip', [
            'name' => $this->name,
            'version' => $this->version
        ]);
        return $result;
    }
}