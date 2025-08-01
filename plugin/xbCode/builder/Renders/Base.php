<?php
namespace plugin\xbCode\builder\Renders;

use JsonSerializable;
use plugin\xbCode\builder\Components\Page;

/**
 * 积木云渲染器基类
 * @copyright 贵州猿创科技有限公司
 * @author 楚羽幽 416716328@qq.com
 */
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
     * 创建组件
     * @param callable $func
     * @param string $url
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    abstract public static function make(callable $func, string $url);

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
            throw new \Exception('请设置当前页面完整地址');
        }
        $urls = parse_url($url);
        if (!isset($urls['path'])) {
            throw new \Exception('请设置正确的当前页面完整地址');
        }
        $path = $urls['path'];
        $query = $urls['query'] ?? '';
        $query = $query ? "?{$query}" : '';
        $this->url = "{$path}{$query}";
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
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     * @return array|Page
     */
    public function jsonSerialize(): mixed
    {
        return $this->create();
    }
}
