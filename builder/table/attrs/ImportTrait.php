<?php

namespace plugin\xbCode\builder\table\attrs;
use plugin\xbCode\builder\ListBuilder;

/**
 * 导入配置
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait ImportTrait
{
    /**
     * 导入配置
     * @Author 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-03-07
     * @param  array       $config
     * @return ListBuilder
     */
    public function importConfig(array $config = []): ListBuilder
    {
        // 开启导入按钮
        $this->setImport();
        // 配置导入参数
        $this->importConfig = array_merge([
            // 服务端导入接口
            'api' => '',
            // 文件类型
            'types' => ['xlsx'],
            // 导入方式 covering, insertBottom, insertTop
            'mode' => 'insertTop',
        ], $config);
        return $this;
    }
}