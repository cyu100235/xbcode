<?php
namespace app\common\enum;

use app\common\Enum;

/**
 * 自定义组件类型
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
class CustomComponent extends Enum
{
    const AMAP = [
        'label'      => '高德地图',
        'value'     => 'amap'
    ];
    const BMAP = [
        'label'      => '百度地图',
        'value'     => 'bmap'
    ];
    const QMAP = [
        'label'      => '腾讯地图',
        'value'     => 'qmap'
    ];
    const REMOTE = [
        'label'      => '远程组件',
        'value'     => 'remote'
    ];
    const UPLOADIFY = [
        'label'      => '附件库',
        'value'     => 'uploadify'
    ];
    const INFO = [
        'label'      => '信息展示',
        'value'     => 'info'
    ];
    const HCODE = [
        'label'      => '代码展示',
        'value'     => 'HCode'
    ];
    const PROMPTTIP = [
        'label'      => '隐藏框',
        'value'     => 'PromptTip'
    ];
    const WANGEDITOR = [
        'label'      => 'wangEditor',
        'value'     => 'wangEditor'
    ];
    const TINYMCEEDITOR = [
        'label'      => 'tinymceEditor',
        'value'     => 'tinymceEditor'
    ];
    const XINPUT = [
        'label'      => '自定义输入框',
        'value'     => 'XInput'
    ];
    const NDivider = [
        'label'      => '虚线框',
        'value'     => 'NDivider'
    ];
    const XTABLE = [
        'label'      => '自定义表格',
        'value'     => 'XTable'
    ];
}
