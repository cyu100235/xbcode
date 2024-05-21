<?php
namespace app\service\cloud;

use app\builder\FormBuilder;
use app\model\Settings;
use app\providers\ConfigFormProvider;
use app\providers\ConfigProvider;
use app\providers\UploadProvider;
use app\utils\JsonUtil;
use support\Request;
use Exception;

/**
 * 插件云服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait PluginsCloud
{
    use JsonUtil;

    /**
     * 获取本地已安装插件
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getLocalPlugin()
    {
        $pluginPath = base_path('plugin/');
        $infoPath   = $pluginPath . '*/info.json';
        $plugins    = glob($infoPath);
        $data       = [];
        foreach ($plugins as $key => $value) {
            $item       = json_decode(file_get_contents($value), true);
            $data[$key] = $item;
        }
        return $data;
    }

    /**
     * 获取本地已安装插件名称
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getLocalPluginName()
    {
        $plugins = self::getLocalPlugin();
        if (empty($plugins)) {
            return [];
        }
        $plugins = array_column($plugins, 'name');
        return $plugins;
    }

    /**
     * 获取本地插件版本
     * @param string $name
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getLocalPluginVersion(string $name)
    {
        $data = self::getLocalPlugin();
        $data = array_column($data, 'version', 'name');
        return $data[$name] ?? '';
    }

    /**
     * 获取本地插件依赖
     * @param string $name
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getLocalPluginDepend(string $name)
    {
        $data = self::getLocalPlugin();
        $data = array_column($data, 'depend', 'name');
        return $data[$name] ?? [];
    }

    /**
     * 检测插件是否已安装
     * @param string $name
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function checkPluginInstall(string $name)
    {
        $pluginNames = self::getLocalPluginName();
        if (in_array($name, $pluginNames)) {
            return true;
        }
        return false;
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
        $active  = $request->get('active', 'plugins');
        $keyword = $request->get('keyword', '');
        $page    = (int) $request->get('page', 1);
        $limit   = (int) $request->get('limit', 20);
        $params  = [
            'keyword' => $keyword,
            'page' => $page,
            'limit' => $limit,
        ];
        // 过滤未安装
        if ($active === 'plugins' && $plugins) {
            $params['filter'] = $plugins;
        }
        // 本地未有安装插件
        if ($active === 'plugins' && empty($plugins)) {
            $params['filter'] = 'all';
        }
        $data = self::getPluginList($params);
        if (empty($data['data'])) {
            return self::successRes($data);
        }
        $list = $data['data'] ?? [];
        foreach ($list as &$value) {
            // 插件版本
            $value['plugin_version'] = "<div style='font-size:13px;'>最新：{$value['version']}</div>";
            $localVersion            = self::getLocalPluginVersion($value['name']);
            if ($localVersion) {
                $value['plugin_version'] .= "<div style='font-size:13px;'>本地：{$localVersion}</div>";
            }
            // 插件状态：10未购买，20未安装，30已安装，40有更新
            $value['plugin_state'] = '10';
            // 配置项：10无，20有
            $value['plugin_config'] = '10';
            // 检测是否已购买
            if ($value['is_buy'] === '20') {
                $value['plugin_state'] = '20';
            }
            // 检测是否安装
            if (self::checkPluginInstall($value['name'])) {
                // 已安装
                $value['plugin_state'] = '30';
                // 是否有配置项
                $config = self::getLocalPluginConfig($value['name']);
                if ($config) {
                    $value['plugin_config'] = '20';
                }
                // 检测是否有更新
                if ($localVersion && version_compare($value['version'], $localVersion)) {
                    $value['plugin_state'] = '40';
                }
            }
            // 插件价格
            $value['price_html'] = "<div style='color:#f56c6c;font-weight:700;'>免费</div>";
            if ($value['price'] > 0) {
                $money               = "<div style='color:#f56c6c;font-weight:700;'>￥{$value['price']}</div>";
                $value['price_html'] = $money;
            }
            // 插件信息
            $value['plugin_title'] = "名称：{$value['title']}";
            $value['plugin_name']  = "标识：{$value['name']}";
        }
        $data['data'] = $list;
        // 返回数据
        return self::successRes($data);
    }

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
        $data['plugin_state'] = '10';
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
            $localVersion = self::getLocalPluginVersion($data['name']);
            $data['local_version'] = $localVersion;
            if ($localVersion && version_compare($data['version'], $localVersion)) {
                $data['plugin_state'] = '40';
            }
        }
        // 返回数据
        return $data;
    }
    
    /**
     * 获取本地插件配置项
     * @param string $name
     * @param mixed $default
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getLocalPluginConfig(string $name,mixed $default = [])
    {
        $class = "\\plugin\\{$name}\\Install";
        if (!class_exists($class)) {
            return $default;
        }
        $methodName = 'config';
        if (!method_exists($class, $methodName)) {
            return $default;
        }
        $data = call_user_func([new $class, $methodName]);
        if (!is_array($data)) {
            return $default;
        }
        return $data;
    }

    /**
     * 获取插件配置
     * @param string $name
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function pluginConfig(Request $request)
    {
        $name = $request->get('name', '');
        if ($request->method() === 'PUT') {
            $post = $request->post();
            foreach ($post as $field => $value) {
                $where = [
                    'group'     => $name,
                    'name'      => $field,
                ];
                $model = Settings::where($where)->find();
                if (!$model) {
                    $model = new Settings;
                    $model->group = $name;
                    $model->name  = $field;
                }
                if (!$model->save(['value'=> $value])) {
                    return self::fail('配置保存失败');
                }
            }
            return self::success('配置保存成功');
        }
        $active  = '';
        $template = self::getLocalPluginConfig($name);
        $config = ConfigProvider::getOriginal($name, []);
        $config = ConfigProvider::parseData($config);
        if ($template) {
            $active = current($template)['field'] ?? '';
        }
        $builder = new FormBuilder;
        $builder->initTabsActive('active', $active, [
            'props'             => [
                // 选项卡样式
                'tabPosition'   => 'top',
            ],
        ]);
        foreach ($template as $value) {
            if (!isset($value['title'])) {
                throw new Exception('配置项标题不能为空');
            }
            if (!isset($value['field'])) {
                throw new Exception('配置项字段不能为空');
            }
            if (!isset($value['children'])) {
                throw new Exception('配置项表单内容不能为空');
            }
            $children = $value['children'] ?? [];
            $formRow = ConfigFormProvider::getFormView($children)->getBuilder()->formRule();
            $builder->addTab($value['field'] ?? '', $value['title'] ?? '', $formRow);
        }
        $builder->endTabs();
        $builder->setMethod('PUT');
        $builder->setFormData($config);
        $data = $builder->create();
        return self::successRes($data);
    }
}