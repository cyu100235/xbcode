<?php

namespace xbcode\builder\table\attrs;
use xbcode\builder\ListBuilder;

/**
 * 表格描述特性
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait DescTrait
{
    /**
     * 描述配置
     * @var array
     */
    protected $headerDesc = [
        'content' => '',
        // el-alert参数
        'props'  => [],
        // el-alert内部div样式
        'style'  => []
    ];
    
    /**
     * 添加描述
     * @param string $content 描述内容
     * @param array $props alert参数
     * @param array $style style参数
     * @return \xbcode\builder\ListBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function addDesc(string $content,array $props = [],array $style = []): ListBuilder
    {
        $this->headerDesc = [
            'content' => $content,
            'props'  => $props,
            'style'  => $style,
        ];
        return $this;
    }
}