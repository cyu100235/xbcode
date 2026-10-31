<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Components\Table;

use plugin\xbCode\builder\Components\Tpl;

/**
 * 键值跳转列
 *
 * 单元格渲染若干行「标签：值」；调用 link() 后整格点击按单页方式跳转，
 * 点击事件由组件内部生成，无需外部再设置 onEvent。
 * 调用 isBack() 会在跳转地址追加 redirect 参数（当前页面地址），
 * 目标页面右上角据此显示「返回」按钮。
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class KeyValueLinkColumn extends Tpl
{
    /**
     * 跳转地址（支持 amis 表达式）
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private string $link = '';

    /**
     * 当前页面地址，用于 isBack 追加返回参数
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private string $currentUrl = '';

    /**
     * 构造函数
     * @param string $currentUrl 当前页面地址
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function __construct(string $currentUrl = '')
    {
        $this->currentUrl = $currentUrl;
    }

    /**
     * 设置点击跳转地址并生成点击事件
     * @param string $link 目标页面地址，支持 amis 表达式
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function link(string $link)
    {
        $this->link = $link;
        $this->applyLinkEvent();
        return $this;
    }

    /**
     * 在跳转地址上追加返回地址
     *
     * 追加 redirect 参数（当前页面地址），目标页面据此显示「返回」按钮；
     * 返回地址只保留后台路由与查询条件，剔除 _act 等内部参数。
     * @param string $path 指定跳转地址，为空时使用 link() 已设置的地址
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function isBack(string $path = '')
    {
        $link = $path !== '' ? $path : $this->link;
        if ($link === '') {
            return $this;
        }
        // 跳转地址只保留路由与业务参数，redirect 单独编码追加
        $link = $this->removeQueryArgs($link, ['redirect', '_act']);
        $separator = str_contains($link, '?') ? '&' : '?';
        $this->link = $link . $separator . 'redirect=' . rawurlencode($this->sanitizeUrl($this->currentUrl));
        $this->applyLinkEvent();
        return $this;
    }

    /**
     * 点击后回填筛选表单并提交查询
     *
     * 用于「点单元格即按当前行数据筛选」的场景：先把指定字段写进顶部筛选表单，
     * 再提交该表单触发列表查询，效果与手动填写条件后点「查询」一致。
     * @param string $filterId 目标筛选表单组件ID
     * @param array $values 回填字段与取值，取值支持 amis 表达式
     * @param string $expression 执行条件表达式，为空表示总是执行；
     * 如 `'${device_uniq}'` 表示当前行脚本ID非空时才回填并查询
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function filterSubmit(string $filterId, array $values, string $expression = '')
    {
        $setValue = [
            'actionType' => 'setValue',
            'componentId' => $filterId,
            'args' => [
                'value' => $values,
            ],
        ];
        $submit = [
            'actionType' => 'submit',
            'componentId' => $filterId,
        ];
        if ($expression !== '') {
            $setValue['expression'] = $expression;
            $submit['expression'] = $expression;
        }
        $this->onEvent([
            'click' => [
                'actions' => [$setValue, $submit],
            ],
        ]);
        return $this;
    }

    /**
     * 生成单页跳转的点击事件
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function applyLinkEvent(): void
    {
        $this->onEvent([
            'click' => [
                'actions' => [
                    [
                        'actionType' => 'link',
                        'args' => [
                            'link' => $this->link,
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * 移除地址上的指定查询参数
     * 保持其余参数的原始写法（如 ${字段} 表达式不被编码）。
     * @param string $url 原始地址
     * @param array $keys 需要移除的参数名
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function removeQueryArgs(string $url, array $keys): string
    {
        $urls = parse_url($url);
        $path = $urls['path'] ?? $url;
        $query = $urls['query'] ?? '';
        if ($query === '') {
            return $path;
        }
        $parts = [];
        foreach (explode('&', $query) as $item) {
            if ($item === '') {
                continue;
            }
            $name = explode('=', $item, 2)[0];
            if (in_array($name, $keys, true)) {
                continue;
            }
            $parts[] = $item;
        }
        return $parts ? $path . '?' . implode('&', $parts) : $path;
    }

    /**
     * 整理当前页面地址
     *
     * 只保留后台路由与业务筛选条件，剔除协议、域名、内部参数 _act/redirect
     * 以及分页、工具栏等渲染器内部状态，避免返回地址里的内部参数
     * 在目标页面被再次解码后污染查询接口（导致返回 schema 而非列表数据）。
     * @param string $url 原始地址
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function sanitizeUrl(string $url): string
    {
        $urls = parse_url($url);
        $path = $urls['path'] ?? '';
        $query = $urls['query'] ?? '';
        if ($query === '') {
            return $path;
        }
        $parts = [];
        foreach (explode('&', $query) as $item) {
            if ($item === '') {
                continue;
            }
            $name = rawurldecode(explode('=', $item, 2)[0]);
            if ($this->isIgnoredQueryKey($name)) {
                continue;
            }
            $parts[] = $item;
        }
        return $parts ? $path . '?' . implode('&', $parts) : $path;
    }

    /**
     * 是否忽略该查询参数
     * @param string $name 参数名
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function isIgnoredQueryKey(string $name): bool
    {
        if (in_array($name, ['_act', 'redirect', 'page', 'perPage', 'limit', 'orderBy', 'orderDir'], true)) {
            return true;
        }
        // 工具栏、地址栏同步等渲染器内部状态
        return str_starts_with($name, 'toolbar[');
    }
}
