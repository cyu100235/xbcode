<?php
namespace plugin\xbDeveloper\api;

use plugin\xbCode\api\DebugApi;

/**
 * 插件管理
 * @copyright 贵州积木云网络网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class PluginsApi
{
    /**
     * 创建插件
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function create(array $data)
    {
        // 数据验证
        PluginsCreate::validate($data);
        // 是否调试模式
        $debug = DebugApi::status();
        // 创建插件
        PluginsCreate::create($data, $debug);
    }

    /**
     * 导出插件
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function export(){}
}