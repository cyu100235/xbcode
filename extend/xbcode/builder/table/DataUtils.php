<?php

namespace xbcode\builder\table;

/**
 * 数据处理工具类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class DataUtils
{
    /**
     * 处理实时表格数据
     * @param array $data
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function realTable(array &$data)
    {
        $result = [];
        if (isset($data['realTable'])) {
            $result = $data['realTable'];
            unset($data['realTable']);
        }
        return $result;
    }

    /**
     * 筛选查询
     * @param array $data
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function formConfig(array &$data)
    {
        $result = [];
        if (isset($data['realTable'])) {
            $result = $data['realTable'];
            unset($data['realTable']);
        }
        return $result;
    }

    /**
     * 处理选项卡数据
     * @param array $data
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function tabsConfig(array &$data)
    {
        $result = [];
        if (isset($data['tabsConfig'])) {
            $result = $data['tabsConfig'];
            unset($data['tabsConfig']);
        }
        return $result;
    }

    /**
     * 处理顶部按钮数据
     * @param array $data
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function topButton(array &$data)
    {
        $result = [];
        if (isset($data['topButtonList'])) {
            $result = $data['topButtonList'];
            unset($data['topButtonList']);
        }
        return $result;
    }

    /**
     * 处理扩展按钮数据
     * @param array $data
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function extraButton(array &$data)
    {
        $result = [];
        if (isset($data['extraButtonList'])) {
            $result = $data['extraButtonList'];
            unset($data['extraButtonList']);
        }
        return $result;
    }

    /**
     * 处理右侧按钮数据
     * @param array $data
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function rightButton(array &$data)
    {
        $result = [];
        if (isset($data['rightButtonList'])) {
            $result = $data['rightButtonList'];
            unset($data['rightButtonList']);
        }
        return $result;
    }
}