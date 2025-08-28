<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @version  1.0
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Components\Form;

/**
 * 解析 Excel
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-excel
 * @method $this allSheets(bool $value) 是否解析所有sheet
 * @method $this parseMode(string $value) 解析模式，支持 'array' 或 'object'
 * @method $this includeEmpty(bool $value) 是否包含空值，默认为 true
 * @method $this plainText(bool $value) 是否解析为纯文本，默认为 true
 * @method $this placeholder(string $value) 占位文本提示，默认为 "拖拽 Excel 到这，或点击上传"
 * @method $this autoFill(array $value) 自动填充，键值对形式
 */
class InputExcel extends FormBase
{
    public string $type = 'input-excel';
}
