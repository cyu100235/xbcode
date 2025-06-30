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
 * 富文本编辑器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-rich-text
 * @method $this saveAsUbb(string $value) 是否保存为 ubb 格式
 * @method $this receiver(string $value) 默认的图片保存 API
 * @method $this videoReceiver(string $value) 默认的视频保存 API 仅支持 froala 编辑器
 * @method $this fileField(string $value) 上传文件时的字段名
 * @method $this size(string $value) 框的大小，可设置为 md 或者 lg
 * @method $this options(array $value) 需要参考 tinymce 或 froala 的文档
 * @method $this buttons(array $value) froala 专用，配置显示的按钮，tinymce 可以通过前面的 options 设置 toolbar 字符串
 */
class InputRichText extends FormBase
{
    public string $type = 'input-rich-text';
}
