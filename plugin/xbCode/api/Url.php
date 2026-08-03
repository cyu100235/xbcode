<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @version  1.0
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\api;

use JsonSerializable;

/**
 * URL生成器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class Url implements JsonSerializable
{
    /**
     * 生成地址
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $path;

    /**
     * 插件名称
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $plugin = '';

    /**
     * 携带参数
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $query = [];

    /**
     * URL协议
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $schema = '';

    /**
     * 生成完整域名URL
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $domain = '';

    /**
     * 模块名称
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $module = '';

    /**
     * 控制器名称
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $controller = '';

    /**
     * 操作方法名称
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $action = '';

    /**
     * 生成前缀斜杠
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected bool $slash = false;

    /**
     * 是否对URL进行编码，默认不编码
     * @var bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected bool $encode = false;

    /**
     * 创建实例
     * @param string $path 控制器/方法名
     * @return Url
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    /**
     * 创建实例
     * @param string $path 模块/控制器/方法名
     * @param bool $init 是否初始化
     * @return Url
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function make(string $path, bool $init = true)
    {
        $class = new self;
        $class->path = $path;
        if ($init) {
            $class->plugin();
            $class->module();
            $class->controller();
            $class->action();
            $class->slash();
        }
        return $class;
    }

    /**
     * 携带参数
     * @param array $query
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function query(array $query = [])
    {
        $this->query = $query;
        return $this;
    }

    /**
     * 设置插件名称
     * @param string $plugin
     * @return Url
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function plugin(string|bool $plugin = '')
    {
        if (empty($plugin) && $plugin !== false) {
            $plugin = empty(request()->plugin) ? '' : request()->plugin;
        }
        if ($plugin === false) {
            $plugin = '';
        }
        $this->plugin = $plugin;
        return $this;
    }

    /**
     * 设置域名协议
     * @param string $schema
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function schema(string $schema = 'http')
    {
        $this->schema = $schema;
        return $this;
    }

    /**
     * 获取协议类型
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getSchema(): string
    {
        if (empty($this->schema)) {
            $header = request()->header();
            $schema = 'http';
            if (!empty($header['origin'])) {
                if (str_contains($header['origin'], 'http://')) {
                    $schema = 'http';
                } elseif (str_contains($header['origin'], 'https://')) {
                    $schema = 'https';
                }
            }
            if (empty($schema) && !empty($header['referer'])) {
                if (str_contains($header['referer'], 'http://')) {
                    $schema = 'http';
                } elseif (str_contains($header['referer'], 'https://')) {
                    $schema = 'https';
                }
            }
            $this->schema = $schema;
        }
        return $this->schema;
    }

    /**
     * 设置生成域名
     * @param string $domain
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setDomain(string $domain = '')
    {
        if (is_bool($domain) && !$domain) {
            return $this;
        }
        if (empty($this->schema)) {
            $this->schema();
        }
        $this->domain = $domain ?: request()->host();
        return $this;
    }

    /**
     * 设置模块名称
     * @param string $value
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function module(string|bool $value = '')
    {
        if (empty($value) && $value !== false) {
            $value = empty(request()->app) ? '' : request()->app;
        }
        if ($value === false) {
            $value = '';
        }
        $this->module = $value;
        return $this;
    }

    /**
     * 设置控制器
     * @param string $value
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function controller(string $value = '')
    {
        if (empty($value)) {
            $controller = '';
            if (!empty(request()->controller)) {
                $controller = request()->controller;
            }
            $_suffix = config('plugin.xbCode.app.controller_suffix', 'Controller');
            $value = basename(str_replace('\\', '/', $controller));
            $value = str_replace($_suffix, '', $value);
        }
        $this->controller = $value;
        return $this;
    }

    /**
     * 设置操作方法
     * @param string $value
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function action(string $value = '')
    {
        if (empty($value)) {
            $value = empty(request()->action) ? '' : request()->action;
        }
        $this->action = $value;
        return $this;
    }

    /**
     * 允许生成前斜杠
     * @param string $value
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function slash(bool $value = true)
    {
        $this->slash = $value;
        return $this;
    }

    /**
     * 设置是否进行参数编码
     * @param bool $value
     * @return Url
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function encode(bool $value = true)
    {
        $this->encode = $value;
        return $this;
    }

    /**
     * 生成地址
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function create()
    {
        $path = trim($this->path, '/');
        $expl = explode('/', $path);
        switch (count($expl)) {
            // 方法
            case 1:
                $this->action = $expl[0];
                break;
            // 控制器/方法
            case 2:
                $this->controller = $expl[0];
                $this->action = $expl[1];
                break;
            // 模块/控制器/方法
            case 3:
                $this->module = $expl[0];
                $this->controller = $expl[1];
                $this->action = $expl[2];
                break;
        }
        $url = "{SCHEMA}://{DOMAIN}{SLASH}app/{PLUGIN}/{MODULE}/{CONTROLER}/{ACTION}";
        // 替换协议
        $url = $this->schema ? str_replace('{SCHEMA}', $this->schema, $url) : str_replace('{SCHEMA}://', '', $url);
        // 替换域名
        $url = $this->domain ? str_replace('{DOMAIN}', $this->domain, $url) : str_replace('{DOMAIN}', '', $url);
        $url = $this->domain ? str_replace('{SLASH}', '/', $url) : $url;
        // 替换插件名称
        $url = $this->plugin ? str_replace('{PLUGIN}', $this->plugin, $url) : str_replace('{PLUGIN}/', '', $url);
        // 替换模块名称
        $url = $this->module ? str_replace('{MODULE}', $this->module, $url) : str_replace('{MODULE}/', '', $url);
        // 替换控制器
        $url = $this->controller ? str_replace('{CONTROLER}', $this->controller, $url) : str_replace('{CONTROLER}/', '', $url);
        // 替换方法名
        $url = $this->action ? str_replace('{ACTION}', $this->action, $url) : str_replace('{ACTION}', '', $url);
        // 处理前斜杠
        $url = $this->slash ? str_replace('{SLASH}', '/', $url) : str_replace('{SLASH}', '', $url);
        // 处理携带参数
        if ($this->query) {
            // 如果query参数是字符串，则直接使用(默认是转义)
            $query = http_build_query($this->query);
            $query = '?' . ($this->encode ? $query : urldecode($query));
            $url .= $query;
        }
        return $url;
    }

    /**
     * 获取域名
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function domain()
    {
        $this->schema();
        $this->getSchema();
        $this->setDomain();
        $domain = "{$this->schema}://{$this->domain}";
        return $domain;
    }

    /**
     * 获取生成URL
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function get()
    {
        return $this->create();
    }

    /**
     * 将URL转换为字符串
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function __toString()
    {
        return $this->create();
    }

    /**
     * 将对象序列化为字符串
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function jsonSerialize(): string
    {
        return $this->create();
    }
}