<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\slot;

/**
 * 列表数据插槽上下文
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class ListContext
{
    /**
     * 当前页数据行
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $items;

    /**
     * 分页上下文
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $meta;

    /**
     * 构造函数
     * @param array $items 当前页数据行
     * @param array $meta 分页上下文（page/limit/total/params）
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function __construct(array $items, array $meta = [])
    {
        $this->items = $items;
        $this->meta  = array_merge([
            'page'   => 1,
            'limit'  => 0,
            'total'  => 0,
            'params' => [],
        ], $meta);
    }

    /**
     * 从分页结果创建上下文
     * @param mixed $paginator 分页对象（支持 toArray）或数组
     * @param array $params 请求参数
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function fromPaginator(mixed $paginator, array $params = []): static
    {
        $result = method_exists($paginator, 'toArray') ? $paginator->toArray() : (array) $paginator;
        return new static($result['data'] ?? [], [
            'page'   => (int) ($result['current_page'] ?? 1),
            'limit'  => (int) ($result['per_page'] ?? 0),
            'total'  => (int) ($result['total'] ?? 0),
            'params' => $params,
        ]);
    }

    /**
     * 导出为成功响应数据（配合 successData 使用）
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function toResult(): array
    {
        return [
            'data'         => $this->items,
            'current_page' => $this->page(),
            'total'        => $this->total(),
        ];
    }

    /**
     * 获取当前页数据行
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * 覆盖当前页数据行
     * @param array $items
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setItems(array $items): static
    {
        $this->items = $items;
        return $this;
    }

    /**
     * 遍历每一行数据（引用方式，可直接修改行字段）
     * @param callable $callback 签名：function(array &$row, int $index, ListContext $ctx)
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function each(callable $callback): static
    {
        foreach ($this->items as $index => &$row) {
            $callback($row, $index, $this);
        }
        unset($row);
        return $this;
    }

    /**
     * 为每一行追加字段
     * @param string $field 字段名
     * @param callable $resolver 解析回调，签名：function(array $row, int $index, ListContext $ctx): mixed
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function append(string $field, callable $resolver): static
    {
        return $this->each(function (array &$row, int $index, ListContext $ctx) use ($field, $resolver) {
            $row[$field] = $resolver($row, $index, $ctx);
        });
    }

    /**
     * 按映射表为每一行追加字段
     * @param string $field 追加字段名
     * @param array $map 映射表 [键 => 值]
     * @param string $key 行中用于取映射的字段名
     * @param mixed $default 未命中时的默认值
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function appendMap(string $field, array $map, string $key = 'id', mixed $default = null): static
    {
        return $this->append($field, function (array $row) use ($map, $key, $default) {
            $value = $row[$key] ?? null;
            return $map[$value] ?? $default;
        });
    }

    /**
     * 过滤数据行
     * @param callable $callback 签名：function(array $row, int $index, ListContext $ctx): bool
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function filter(callable $callback): static
    {
        $result = [];
        foreach ($this->items as $index => $row) {
            if ($callback($row, $index, $this)) {
                $result[] = $row;
            }
        }
        $this->items = $result;
        return $this;
    }

    /**
     * 映射（整体替换）数据行
     * @param callable $callback 签名：function(array $row, int $index, ListContext $ctx): array
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function map(callable $callback): static
    {
        $result = [];
        foreach ($this->items as $index => $row) {
            $result[] = $callback($row, $index, $this);
        }
        $this->items = $result;
        return $this;
    }

    /**
     * 提取某一列的值（常用于批量查询聚合）
     * @param string $field 字段名
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function column(string $field): array
    {
        $values = [];
        foreach ($this->items as $row) {
            if (isset($row[$field])) {
                $values[] = $row[$field];
            }
        }
        return $values;
    }

    /**
     * 获取分页页码
     * @return int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function page(): int
    {
        return (int) $this->meta['page'];
    }

    /**
     * 获取每页条数
     * @return int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function limit(): int
    {
        return (int) $this->meta['limit'];
    }

    /**
     * 获取总记录数
     * @return int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function total(): int
    {
        return (int) $this->meta['total'];
    }

    /**
     * 获取请求参数
     * @param string $key 参数名，为空返回全部
     * @param mixed $default 默认值
     * @return mixed
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function param(string $key = '', mixed $default = null): mixed
    {
        if ($key === '') {
            return $this->meta['params'];
        }
        return $this->meta['params'][$key] ?? $default;
    }
}