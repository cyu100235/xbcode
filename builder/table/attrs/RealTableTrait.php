<?php

namespace plugin\xbCode\builder\table\attrs;

/**
 * 实时表格
 * @author 贵州小白基地网络科技有限公司
 * @copyright 贵州小白基地网络科技有限公司
 * @email cy958416459@qq.com
 */
trait RealTableTrait
{
    /**
     * 实时表格
     * @var array
     * @author 贵州小白基地网络科技有限公司
     * @email cy958416459@qq.com
     */
    protected $realTable    = [
        // 是否开启实时表格
        'enable'            => false,
        // 表格实时刷新时间间隔（单位：秒）
        'time_interval'     => 10,
    ];

    /**
     * 开启实时表格
     * @param bool $enable
     * @param int $time_interval
     * @return mixed
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     * @email cy958416459@qq.com
     */
    public function realTable(bool $enable = true, int $time_interval = 10)
    {
        $this->realTable['enable'] = $enable;
        $this->realTable['time_interval'] = $time_interval;
        return $this;
    }
}