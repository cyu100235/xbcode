<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Renders;

use Exception;
use JsonSerializable;
use plugin\xbCode\builder\Components\Page;

/**
 * 积木云渲染器基类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
#[\AllowDynamicProperties]
abstract class Base implements JsonSerializable
{
    /**
     * 渲染页面组件
     * @var Page
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected Page $page;

    /**
     * 主键键名
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $primaryKey = 'id';

    /**
     * 当前页面完整地址
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $url;

    /**
     * 获取数据请求类型
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $method = 'get';

    /**
     * 重定向地址
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $redirect = '';

    /**
     * 组件实例
     * @param string $url
     * @return static
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    abstract public static function instance(string $url);

    /**
     * 创建组件
     * @param callable $callback 创建组件
     * @return static
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    abstract public static function make(?callable $callback);

    /**
     * 获取当前地址
     * @param string $url 当前页面地址
     * @param array $querys 附带参数
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function getCurrentPageUrl(string $url = '', array $querys = []): string
    {
        if (empty($url)) {
            $url = request()->fullUrl();
        }
        $urls = parse_url($url);
        if (!isset($urls['path'])) {
            throw new Exception('请设置正确的接口地址');
        }
        $path = $urls['path'];
        $query = $urls['query'] ?? '';
        parse_str($query, $params);
        $params = empty($params) ? [] : $params;
        $params = array_merge($params, $querys);
        $query = http_build_query($params);
        $query = $query ? "?{$query}" : '';
        $url = "{$path}{$query}";
        return $url;
    }

    /**
     * 获取页面组件实例
     * @return Page
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function usePage()
    {
        return $this->page;
    }

    /**
     * 设置主键键名
     * @param string $key
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setPrimaryKey(string $key)
    {
        $this->primaryKey = $key;
        return $this;
    }

    /**
     * 获取主键键名
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    /**
     * 设置当前页面完整地址
     * @param string $url
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setUrl(string $url)
    {
        if (empty($url)) {
            throw new Exception('请设置当前页面完整地址');
        }
        // 编码URL地址
        $url = urldecode($url);
        $urls = parse_url($url);
        if (!isset($urls['path'])) {
            throw new Exception('请设置正确的当前页面完整地址');
        }
        $querys = [];
        if (isset($urls['query'])) {
            parse_str($urls['query'], $querys);
        }
        $path = $urls['path'];
        $query = $urls['query'] ?? '';
        $query = $query ? "?{$query}" : '';
        $this->url = "{$path}{$query}";
        $this->redirect = $querys['_redirect'] ?? '';
        return $this;
    }
    
    /**
     * 设置重定向地址
     * @param string $url
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setRedirect(string $url)
    {
        $this->redirect = $url;
        return $this;
    }
    
    /**
     * 获取当前页面完整地址
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * 获取渲染器规则
     * @return mixed
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    abstract public function create():mixed;

    /**
     * 获取JSON序列化数据
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     * @return array|Page
     */
    public function jsonSerialize(): mixed
    {
        return $this->create();
    }
}
