<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\api;

use Exception;
use plugin\xbCode\builder\Builder;
use plugin\xbCode\builder\Renders\XbForm;
use plugin\xbCode\builder\Renders\XbTabForm;

/**
 * 配置视图接口类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class ConfigView
{

    /**
     * 获取普通表单渲染器
     * @param string $path 配置文件路径
     * @throws \Exception
     * @return XbForm
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getConfigBuilder(string $path)
    {
        // 创建表单渲染器
        return Builder::form(function (XbForm $builder) use ($path) {
            // 获取配置模板数据
            $template = static::getConfigTemplate($path, 'config');
            foreach ($template as $value) {
                if (!isset($value['name'])) {
                    throw new Exception('字段参数错误');
                }
                if (!isset($value['type'])) {
                    throw new Exception('组件类型参数错误');
                }
                $builder->addRowRenderComponent($value);
            }
        });
    }

    /**
     * 获取选项卡表单视图
     * @param string $path 配置文件路径
     * @return XbTabForm
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getTabsBuilder(string $path)
    {
        return Builder::tabForm(function (XbTabForm $builder) use ($path) {
            // 获取配置模板数据
            $template = static::getConfigTemplate($path, 'tabs');
            foreach ($template as $tab) {
                if (empty($tab['title'])) {
                    throw new Exception('选项卡标题参数错误');
                }
                if (empty($tab['name'])) {
                    throw new Exception('选项卡标识参数错误');
                }
                if (empty($tab['body'])) {
                    throw new Exception('选项卡子配置参数错误');
                }
                $builder->addTab($tab['name'], $tab['title'], $tab['body'] ?? []);
            }
        });
    }

    /**
     * 获取配置模板数据
     * @param string $path 配置路径：插件名称/配置文件
     * @param string $type 配置类型：config普通，tab选项卡
     * @throws \Exception
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getConfigTemplate(string $path, string $type)
    {
        // 获取插件名称
        $plugin = ConfigChecked::getPluginName($path);
        // 获取分组名称
        $groupName = ConfigChecked::getGroupName($path);
        // 获取模板文件
        $filePath = "/plugin/{$plugin}/setting/{$type}/{$groupName}.php";
        // 完整模板文件
        $fullPath = base_path() . $filePath;
        if (!file_exists($fullPath)) {
            throw new Exception("{$filePath} - 配置模板文件不存在");
        }
        // 获取插件配置
        $data = include $fullPath;
        // 返回配置数据
        return $data;
    }
}