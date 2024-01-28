<?php

namespace app\common\builder\table;
use app\common\builder\ListBuilder;

/**
 * 筛选查询
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait PagesTrait
{
    /**
     * 分页配置
     *
     * @Author 贵州小白基地网络科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @param  array       $pagerConfig
     * @param  array       $field
     * @return ListBuilder
     */
    public function pageConfig(array $pagerConfig = [], array $field = []): ListBuilder
    {
        // 当前页码
        $currentPage = (int) request()->get('page', 1);
        // 分页配置
        $config = config('paginator',[]);
        // 每页数量
        $listRows                   = isset($config['listRows']) ? $config['listRows'] : 20;
        $this->pagerConfig          = array_merge([
            'currentPage' => $currentPage,
            'pageSize'    => $listRows,
            'total'       => 1000,
            'pageSizes'   => [10, 15, 20, 50, 100, 200, 500, 1000],
            'align'       => 'right',
            'background'  => false,
            'perfect'     => false,
            'border'      => false,
            'layouts'     => [
                'PrevJump',
                'PrevPage',
                'Number',
                'NextPage',
                'NextJump',
                'Sizes',
                'FullJump',
                'Total'
            ]
        ], $pagerConfig);
        $this->proxyConfig['props'] = array_merge([
            'result' => 'data.data',
            'total'  => 'data.total',
        ], $field);
        return $this;
    }
}