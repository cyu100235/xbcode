<?php
namespace plugin\xbCode\builder\table\attrs;

use plugin\xbCode\builder\ListBuilder;

/**
 * 筛选查询
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait PageTrait
{
    /**
     * 分页配置
     * @var array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private $pagerConfig = [
        'currentPage' => 1,
        'pageSize' => 30,
        'total' => 1000,
        'pageSizes' => [20, 30, 50, 100, 200, 300, 500, 1000],
        'align' => 'right',
        'background' => true,
        'perfect' => false,
        'border' => false,
        'layouts' => [
            'PrevJump',
            'PrevPage',
            'Number',
            'NextPage',
            'NextJump',
            'Sizes',
            'FullJump',
            'Total',
        ],
    ];

    /**
     * 分页配置
     * @param array $pagerConfig
     * @param array $field
     * @return ListBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function pageConfig(array $pagerConfig = [], array $field = []): ListBuilder
    {
        // 当前页码
        $currentPage = (int) request()->get('page', 1);
        // 分页配置
        $config = config('paginator', []);
        // 每页数量
        $listRows = isset($config['listRows']) ? $config['listRows'] : 30;
        // 分页配置
        $pagerConfig['pageSize'] = $listRows;
        $pagerConfig['currentPage'] = $currentPage;
        $this->pagerConfig = array_merge($this->pagerConfig, $pagerConfig);
        // 分页配置
        $this->proxyConfig['props'] = array_merge([
            'result' => 'data.data',
            'total' => 'data.total',
        ], $field);
        return $this;
    }
}