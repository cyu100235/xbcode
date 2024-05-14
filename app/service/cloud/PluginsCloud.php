<?php
namespace app\service\cloud;
use Exception;

/**
 * 插件云服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait PluginsCloud
{
    /**
     * 获取插件列表
     * @param string $keyword
     * @param int $page
     * @param int $limit
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function pluginList(string $keyword = '',int $page = 1, int $limit = 20)
    {
        $data = [
            'keyword' => $keyword,
            'page' => $page,
            'limit' => $limit
        ];
        $result = HttpCloud::get('Plugins/index',$data);
        // 数据验证
        $data = HttpCloud::getContent($result);
        return $data;
    }

    /**
     * 获取插件详情
     * @param string $name
     * @param string $version
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function pluginDetail(string $name,string $version)
    {
        $data = [
            'name' => $name,
            'version' => $version,
        ];
        $result = HttpCloud::get('Plugins/detail',$data);
        $data = HttpCloud::getContent($result);
        return $result['data'] ?? [];
    }
}