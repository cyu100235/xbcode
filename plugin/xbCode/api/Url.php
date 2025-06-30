<?php
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
    protected string $plugin;

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
     * 是否生成模块地址
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $module = '';

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
    public static function make(string $path)
    {
        $class = new self;
        $class->path = $path;
        $class->plugin(request()->plugin ?: 'xbCode');
        $class->module(request()->app);
        $class->slash();
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
    public function plugin(string $plugin)
    {
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
     * 设置生成域名
     * @param string $domain
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function domain(string $domain = '')
    {
        if(empty($this->schema)){
            $this->schema();
        }
        $this->domain = $domain ?: request()->host();
        return $this;
    }

    /**
     * 设置模块名称
     * @param string $module
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function module(string $module)
    {
        $this->module = $module;
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
        $url = "{SCHEMA}://{DOMAIN}{SLASH}app/{PLUGIN}/{MODULE}/{$path}";
        // 替换插件名称
        $url = $this->plugin ? str_replace('{PLUGIN}', $this->plugin, $url) : str_replace('{PLUGIN}/', '', $url);
        // 替换模块名称
        $url = $this->module ? str_replace('{MODULE}', $this->module, $url) : str_replace('{MODULE}/', '', $url);
        // 替换协议
        $url = $this->schema ? str_replace('{SCHEMA}', $this->schema, $url) : str_replace('{SCHEMA}://', '', $url);
        // 替换域名
        $url = $this->domain ? str_replace('{DOMAIN}', $this->domain, $url) : str_replace('{DOMAIN}', '', $url);
        $url = $this->domain ? str_replace('{SLASH}', '/' , $url) : $url;
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
    public function jsonSerialize():string
    {
        return $this->create();
    }
}