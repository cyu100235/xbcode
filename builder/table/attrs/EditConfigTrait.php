<?php

namespace plugin\xbCode\builder\table\attrs;
use plugin\xbCode\builder\ListBuilder;

/**
 * 编辑配置
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait EditConfigTrait
{
    /**
     * 编辑配置
     * @var array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private $editConfig = [
        'enabled'    => true,
        'trigger'    => 'dblclick',
        'mode'       => 'cell',
        'showStatus' => true
    ];
    
    /**
     * 开启编辑
     * @Author 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-03-13
     * @param  array       $config
     * @return ListBuilder
     */
    public function editConfig(array $config = []): ListBuilder
    {
        $this->editConfig = array_merge($this->editConfig, $config);
        return $this;
    }
}