<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\api;

use JsonSerializable;

/**
 * 后台URL生成器
 *
 * 生成规则：{协议}://{域名}/{插件}/{模块}/{控制器}/{方法}
 * 首段必须是插件名，webman 的跨插件路由解析依赖该约定，
 * 因此不能带 app/ 前缀（那是静态资源映射规则）。
 */
class Url implements JsonSerializable
{
    /**
     * 传入的路径
     * @var string
     */
    protected string $path = '';

    /**
     * 插件标识
     * @var string
     */
    protected string $plugin = '';

    /**
     * 模块标识
     * @var string
     */
    protected string $module = '';

    /**
     * 控制器名称
     * @var string
     */
    protected string $controller = '';

    /**
     * 操作方法名称
     * @var string
     */
    protected string $action = '';

    /**
     * 携带参数
     * @var array
     */
    protected array $query = [];

    /**
     * URL协议
     * @var string
     */
    protected string $schema = '';

    /**
     * 域名
     * @var string
     */
    protected string $domain = '';

    /**
     * 是否生成前缀斜杠
     * @var bool
     */
    protected bool $slash = false;

    /**
     * 是否编码参数
     * @var bool
     */
    protected bool $encode = false;

    /**
     * 创建实例
     * @param string $path 方法名 / 控制器/方法名 / 模块/控制器/方法名
     * @param bool $init 是否按当前请求补全插件、模块等信息
     * @return static
     */
    public static function make(string $path, bool $init = true): static
    {
        $class = new static;
        $class->path = $path;
        if ($init) {
            $class->plugin();
            $class->module();
            $class->controller();
            $class->action();
            $class->slash(true);
        }
        return $class;
    }

    /**
     * 携带参数
     * @param array $query
     * @return static
     */
    public function query(array $query = []): static
    {
        $this->query = $query;
        return $this;
    }

    /**
     * 设置插件标识，传 false 清除
     * @param string|bool $plugin
     * @return static
     */
    public function plugin(string|bool $plugin = ''): static
    {
        if ($plugin === false) {
            $plugin = '';
        } elseif ($plugin === '') {
            $plugin = (string) (request()->plugin ?? '');
        }
        $this->plugin = $plugin;
        return $this;
    }

    /**
     * 设置模块标识，传 false 清除
     * @param string|bool $module
     * @return static
     */
    public function module(string|bool $module = ''): static
    {
        if ($module === false) {
            $module = '';
        } elseif ($module === '') {
            $module = (string) (request()->app ?? '');
        }
        $this->module = $module;
        return $this;
    }

    /**
     * 设置控制器名称
     * @param string $controller
     * @return static
     */
    public function controller(string $controller = ''): static
    {
        if ($controller === '') {
            $controller = (string) (request()->controller ?? '');
            $controller = basename(str_replace('\\', '/', $controller));
            $suffix = (string) config('app.controller_suffix', 'Controller');
            if ($suffix !== '') {
                $controller = str_replace($suffix, '', $controller);
            }
        }
        $this->controller = $controller;
        return $this;
    }

    /**
     * 设置操作方法名称
     * @param string $action
     * @return static
     */
    public function action(string $action = ''): static
    {
        if ($action === '') {
            $action = (string) (request()->action ?? '');
        }
        $this->action = $action;
        return $this;
    }

    /**
     * 设置协议
     * @param string $schema
     * @return static
     */
    public function schema(string $schema = 'http'): static
    {
        $this->schema = $schema;
        return $this;
    }

    /**
     * 设置域名
     * @param string|bool $domain
     * @return static
     */
    public function domain(string|bool $domain = ''): static
    {
        if ($domain === false) {
            $this->domain = '';
            return $this;
        }
        $this->domain = $domain !== '' ? $domain : (string) request()->host();
        return $this;
    }

    /**
     * 是否拼接前缀斜杠
     * @param bool $value
     * @return static
     */
    public function slash(bool $value = true): static
    {
        $this->slash = $value;
        return $this;
    }

    /**
     * 是否编码查询参数
     * @param bool $value
     * @return static
     */
    public function encode(bool $value = true): static
    {
        $this->encode = $value;
        return $this;
    }

    /**
     * 生成URL
     * @return string
     */
    public function get(): string
    {
        return $this->create();
    }

    /**
     * 序列化为字符串
     * @return string
     */
    public function jsonSerialize(): string
    {
        return $this->create();
    }

    /**
     * 转字符串
     * @return string
     */
    public function __toString(): string
    {
        return $this->create();
    }

    /**
     * 组装URL
     * @return string
     */
    protected function create(): string
    {
        $this->resolvePath();
        $url = '{SCHEMA}://{DOMAIN}{SLASH}{PLUGIN}/{MODULE}/{CONTROLLER}/{ACTION}';
        // 协议
        $url = $this->schema !== '' ? str_replace('{SCHEMA}', $this->schema, $url) : str_replace('{SCHEMA}://', '', $url);
        // 域名
        $url = str_replace('{DOMAIN}', $this->domain, $url);
        // 插件标识
        $url = $this->plugin !== '' ? str_replace('{PLUGIN}', $this->plugin, $url) : str_replace('{PLUGIN}/', '', $url);
        // 模块标识
        $url = $this->module !== '' ? str_replace('{MODULE}', $this->module, $url) : str_replace('{MODULE}/', '', $url);
        // 控制器
        $url = $this->controller !== '' ? str_replace('{CONTROLLER}', $this->controller, $url) : str_replace('{CONTROLLER}/', '', $url);
        // 方法
        $url = $this->action !== '' ? str_replace('{ACTION}', $this->action, $url) : str_replace('{ACTION}', '', $url);
        // 前缀斜杠
        $url = str_replace('{SLASH}', $this->slash ? '/' : '', $url);
        // 查询参数
        if ($this->query) {
            $query = http_build_query($this->query);
            $url .= '?' . ($this->encode ? $query : urldecode($query));
        }
        return $url;
    }

    /**
     * 按路径层级补全模块/控制器/方法
     * @return void
     */
    protected function resolvePath(): void
    {
        $path = trim($this->path, '/');
        if ($path === '') {
            return;
        }
        $expl = explode('/', $path);
        switch (count($expl)) {
            case 1:
                $this->action = $expl[0];
                break;
            case 2:
                $this->controller = $expl[0];
                $this->action = $expl[1];
                break;
            default:
                $this->module = $expl[0];
                $this->controller = $expl[1];
                $this->action = $expl[2];
        }
    }
}
