<?php

namespace xbcode\builder\table\attrs;

use xbcode\builder\ListBuilder;

/**
 * 顶部组件
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait TopTrait
{
    /**
     * 顶部组件
     * @var array
     */
    protected $topRemote = [
        'file' => '',
        'ajaxParams' => [],
    ];
    
    /**
     * 设置启用远程组件表单
     * @param string $file
     * @param array $params
     * @return ListBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function addTopRemote(string $file, array $params = []): ListBuilder
    {
        $this->topRemote['file']       = $file;
        $this->topRemote['ajaxParams'] = $params;
        return $this;
    }
}