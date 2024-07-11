<?php
namespace app\common\service\cloud;

use support\Request;

/**
 * 插件云服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait PluginsCloud
{
    /**
     * 获取云端插件列表
     * @param array $params
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function getPluginList(array $params)
    {
        $result = HttpCloud::get('Plugins/index', $params);
        // 数据验证
        $data = HttpCloud::getContent($result);
        // 返回数据
        return $data;
    }

    /**
     * 获取插件列表
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function pluginList(Request $request)
    {
        // 获取数据
        $plugins = self::getLocalPluginName();
        $plugins = $plugins ? implode(',', $plugins) : [];
        $keyword = $request->get('keyword', '');
        $page    = (int) $request->get('page', 1);
        $limit   = (int) $request->get('limit', 20);
        $params  = [
            'keyword' => $keyword,
            'page' => $page,
            'limit' => $limit,
        ];
        // 过滤未安装
        $params['filter'] = $plugins;
        // 获取服务端插件
        $data = self::getPluginList($params);
        if (empty($data['data'])) {
            return self::successRes($data);
        }
        $list = $data['data'] ?? [];
        foreach ($list as &$value) {
            // 检测是否已购买
            if ($value['is_buy'] === '20') {
                $value['plugin_state'] = '20';
            }
            // 插件价格
            $value['price_html'] = "<div style='color:#f56c6c;font-weight:700;'>免费</div>";
            if ($value['price'] > 0) {
                $money               = "<div style='color:#f56c6c;font-weight:700;'>￥{$value['price']}</div>";
                $value['price_html'] = $money;
            }
            // 插件信息
            $value['plugin_name'] = "标识：{$value['name']}";
        }
        $data['data'] = $list;
        // 返回数据
        return self::successRes($data);
    }

    /**
     * 获取插件详情
     * @param string $name
     * @param string $version
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function pluginDetail(string $name, string $version)
    {
        $data   = [
            'name' => $name,
            'version' => $version,
        ];
        $result = HttpCloud::get('Plugins/detail', $data);
        $data   = HttpCloud::getContent($result);
        if (empty($data)) {
            return [];
        }
        // 插件状态：10未购买，20未安装，30已安装，40有更新
        $data['plugin_state']  = '10';
        $data['local_version'] = '';
        // 检测是否已购买
        if ($data['is_buy'] === '20') {
            $data['plugin_state'] = '20';
        }
        // 检测是否安装
        if (self::checkPluginInstall($data['name'])) {
            // 已安装
            $data['plugin_state'] = '30';
            // 检测是否有更新
            $localVersion          = self::getLocalPluginVersion($data['name']);
            $data['local_version'] = $localVersion;
            if ($localVersion && version_compare($data['version'], $localVersion)) {
                $data['plugin_state'] = '40';
            }
        }
        // 返回数据
        return $data;
    }
}