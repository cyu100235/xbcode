<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @version  1.0
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Renders\Common;

/**
 * 路由器类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class Router
{
    /**
     * 主键
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $primaryKey = 'id';

    /**
     * 创建Router实例
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function make()
    {
        return new static();
    }

    /**
     * 设置主键
     * @param string $key
     * @return Router
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setPrimaryKey(string $key): self
    {
        $this->primaryKey = $key;
        return $this;
    }

    /**
     * 获取主键
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    /**
     * 获取列表API
     * @param string|array $api
     * @param array $query
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getListUrl(string|array $api, array $query = [])
    {
        return $this->getAPIResult($api, $query);
    }

    /**
     * 获取添加视图API
     * @param string|array $api
     * @param array $query
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getAddViewUrl(string|array $api, array $query = [])
    {
        return $this->getAPIResult($api, $query);
    }

    /**
     * 获取添加保存API
     * @param string|array $api
     * @param array $query
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getAddSaveUrl(string|array $api, array $query = [])
    {
        return $this->getAPIResult($api, $query);
    }

    /**
     * 获取编辑视图API
     * @param string|array $api
     * @param array $query
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getEditViewUrl(string|array $api, array $query = [])
    {
        $query[$this->primaryKey] = '${'.$this->primaryKey.'}';
        return $this->getAPIResult($api, $query);
    }

    /**
     * 获取编辑保存API
     * @param string|array $api
     * @param array $query
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getEditSaveUrl(string|array $api, array $query = [])
    {
        $query[$this->primaryKey] = '${'.$this->primaryKey.'}';
        return $this->getAPIResult($api, $query);
    }

    /**
     * 获取删除API
     * @param string|array $api
     * @param array $query
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getDelSaveUrl(string|array $api, array $query = [])
    {
        $query[$this->primaryKey] = '${'.$this->primaryKey.'}';
        return $this->getAPIResult($api, $query);
    }

    /**
     * 保存排序的API
     * @param string|array $api
     * @param array $query
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getSaveOrderApi(string|array $api,array $query = [])
    {
        return $this->getAPIResult($api, $query);
    }

    /**
     * 快速编辑后用来批量保存的 API
     * @param string|array $api
     * @param array $query
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getQuickSaveApi(string|array $api,array $query = [])
    {
        return $this->getAPIResult($api, $query);
    }

    /**
     * 快速编辑配置成及时保存时使用的 API
     * @param string|array $api
     * @param array $query
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getQuickSaveItemApi(string|array $api,array $query = [])
    {
        return $this->getAPIResult($api, $query);
    }

    /**
     * 获取API结果
     * @param string|array $api
     * @param array $querys
     * @throws \InvalidArgumentException
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getAPIResult(string|array $api, array $querys = [])
    {
        if (is_string($api)) {
            $url = $api;
            $method = 'get';
            $query = $querys;
        } else {
            $url = $api['url'] ?? '';
            $method = $api['method'] ?? 'get';
            $query = $api['query'] ?? [];
            $query = array_merge($query, $querys);
        }
        if (empty($url)) {
            throw new \InvalidArgumentException('请提供有效的API地址');
        }
        $queryString = http_build_query($query);
        if (!empty($queryString)) {
            $url .= (strpos($url, '?') === false ? '?' : '&') . $queryString;
        }
        $method = strtolower($method);
        if (!in_array($method, ['get', 'post', 'put', 'delete'])) {
            throw new \InvalidArgumentException('不支持的HTTP方法: ' . $method);
        }
        return "{$method}:{$url}";
    }
}
