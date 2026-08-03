<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\skills;

use Exception;
use plugin\xbCode\api\PluginsApi;

/**
 * 技能基类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
abstract class SkillBase
{
    /**
     * 插件名称
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected string $pluginTitle = '';

    /**
     * 插件标识
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected string $pluginName = '';

    /**
     * 接口类地址
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private string $class;

    /**
     * 构造函数
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function __construct()
    {
        if (empty($this->pluginTitle)) {
            throw new Exception('技能类插件名称参数错误');
        }
        if (empty($this->pluginName)) {
            throw new Exception('技能类插件标识参数错误');
        }
        // 验证插件是否已安装
        if (!PluginsApi::make()->installed($this->pluginName)) {
            throw new Exception("{$this->pluginTitle} 插件未安装");
        }
        // 验证插件是否已启用
        if (!PluginsApi::make()->hasEnabled($this->pluginName)) {
            throw new Exception("{$this->pluginTitle} 插件未启用");
        }
        // 获取继承类（包含命名空间）
        $class = get_called_class();
        // 获取继承类名称（不含命名空间）
        $class = basename(str_replace('\\', '/', $class));
        // 接口类名称
        $apiClass = str_replace('Skill', 'Api', $class);
        // 验证技能类是否存在
        $class = "\\plugin\\{$this->pluginName}\\api\\{$apiClass}";
        if (!class_exists($class)) {
            throw new Exception("{$this->pluginTitle} 插件的 {$apiClass} 接口类不存在");
        }
        if (!method_exists($class, 'make')) {
            throw new Exception("{$this->pluginTitle} 插件的 {$apiClass} 接口类未实现 make 方法");
        }
        $this->class = $class;
    }

    /**
     * 创建实例
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function make()
    {
        return new static;
    }

    /**
     * 获取目标API实例
     * @return object
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function getApi(): object
    {
        $class = $this->class;
        return $class::make();
    }

    /**
     * 转发方法调用到目标API
     * @param string $method 方法名
     * @param array $arguments 参数
     * @return mixed
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function __call(string $method, array $arguments)
    {
        $api = $this->getApi();
        if (method_exists($api, $method)) {
            return $api->$method(...$arguments);
        }
        throw new Exception("目标接口类不存在方法: {$method}");
    }
}
