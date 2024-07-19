<?php

namespace app\common\builder\table;

/**
 * 表格属性
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait AttrsTrait
{
    // 表格唯一ID
    private $id = 'full_edit_1';
    // 表格高度
    private $height = 'auto';
    // 是否带有边框
    private $border = 'inner';
    // 是否为圆角边框
    private $round = false;
    // 表格的尺寸 medium, small, mini
    private $size = 'medium';
    // 所有的列对齐方式
    private $align = 'left';
    // 所有的表头列的对齐方式
    private $headerAlign = 'left';
    // 所有的表尾列的对齐方式
    private $footerAlign = 'left';
    // 是否显示表头
    private $showHeader = true;
    // 是否显示表尾
    private $showFooter = false;
    // 设置表头所有内容过长时显示为省略号
    private $showHeaderOverflow = true;
    // 设置表尾所有内容过长时显示为省略号
    private $showOverflow = true;
    // 保持原始值的状态，被某些功能所依赖，比如编辑状态、还原数据等（开启后影响性能，具体取决于数据量）
    private $keepSource = false;
    // 数据渠道配置项（基于 Promise API）
    private $proxyConfig = [
        // 启用动态序号渠道，每一页的序号会根据当前页数变化
        'seq'    => true,
        // 启用排序渠道，当点击排序时会自动触发 query 行为
        'sort'   => true,
        // 启用筛选渠道，当点击筛选时会自动触发 query 行为 
        'filter' => true,
        // 启用表单渠道，当点击表单提交按钮时会自动触发 reload 行为
        'form'   => true,
        // 数据渠道字段
        'props'  => [
            // 默认无分页
            'list' => 'data',
        ],
    ];
}