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

/**
 * 插件目录接口
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link http://www.xbcode.net
 * @method $this name(string $value) 字符串注释示例
 */
class PluginPathApi
{
    /**
     * 插件目录标识
     * @var string
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    protected $plugin;

    /**
     * 构造函数
     * @param string $plugin 插件目录标识
     * @throws \Exception
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    public function __construct(string $plugin = '')
    {
        if (empty($plugin)) {
            $plugin = request()->plugin;
        }
        if (empty($plugin)) {
            throw new \Exception('无法获取到插件标识');
        }
        $this->plugin = $plugin;
    }

    /**
     * 获取实例
     * @param string $plugin
     * @return PluginPathApi
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    public static function make(string $plugin = '')
    {
        $instance = new static($plugin);
        return $instance;
    }

    /**
     * 插件根目录
     * @return string
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    public function basePath()
    {
        return base_path("/plugin/{$this->plugin}/");
    }

    /**
     * 插件接口目录
     * @return string
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    public function apiPath()
    {
        $path = $this->basePath() . '/api';
        return $path;
    }

    /**
     * 插件应用目录
     * @return string
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    public function appPath()
    {
        $path = $this->basePath() .'/app';
        return $path;
    }

    /**
     * 获取模块目录
     * @param string $module 模块名称
     * @throws \Exception
     * @return string
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    public function modulePath(string $module = '')
    {
        if (empty($module)) {
            $module = request()->app;
        }
        if (empty($module)) {
            throw new \Exception('获取模块名称失败');
        }
        $path = $this->appPath() ."/{$module}";
        return $path;
    }

    /**
     * 获取模块控制器
     * @param string $module 模块名称
     * @return string
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    public function moduleControllerPath(string $module = '')
    {
        $path = $this->modulePath($module) .'/controller';
        return $path;
    }

    /**
     * 获取模块中间件
     * @param string $module 模块名称
     * @return string
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    public function moduleViewPath(string $module = '')
    {
        $path = $this->modulePath($module) .'/view';
        return $path;
    }

    /**
     * 获取视图文件路径
     * @param string $path 文件路径
     * @param string $module 模块名称
     * @return string
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    public function viewPath(string $path,string $module = '')
    {
        $path = $this->moduleViewPath($module) ."/{$path}";
        return $path;
    }

    /**
     * 插件核心配置目录
     * @return string
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    public function configPath()
    {
        $path = $this->basePath() .'/config';
        return $path;
    }

    /**
     * 插件资源目录
     * @return string
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    public function publicPath()
    {
        $path = $this->basePath() .'/public';
        return $path;
    }

    /**
     * 插件配置目录
     * @return string
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    public function settingPath()
    {
        $path = $this->basePath() .'/setting';
        return $path;
    }
}