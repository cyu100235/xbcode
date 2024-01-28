<?php

namespace app\common\builder\table;
use app\common\builder\ListBuilder;

/**
 * 导入
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait ImportTrait
{
    /**
     * 导入配置
     * @Author 贵州小白基地网络科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @param  array       $config
     * @return ListBuilder
     */
    public function importConfig(array $config = []): ListBuilder
    {
        // 开启导入按钮
        $this->toolbarConfig([
            'import' => true
        ]);
        // 配置导入参数
        $this->importConfig = array_merge([
            'api'      => '',
            'isRemote' => false,
            'types'    => ['xlsx'],
            'modes'    => ['insert'],
        ], $config);
        return $this;
    }
}