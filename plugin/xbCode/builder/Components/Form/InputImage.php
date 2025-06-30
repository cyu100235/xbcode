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
 * 上传图片
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-image
 * @method $this receiver(string $value) 设置上传文件接口
 * @method $this accept(string $value) 设置支持的图片类型格式，请配置此属性为图片后缀，例如.jpg,.png
 * @method $this capture(string $value) 设置用于控制 input[type=file] 标签的 capture 属性，在移动端可控制输入来源
 * @method $this maxSize(int $value) 设置默认没有限制，当设置后，文件大小大于此值将不允许上传。单位为B
 * @method $this maxLength(int $value) 设置默认没有限制，当设置后，一次只允许上传指定数量文件。
 * @method $this multiple(bool $value) 设置是否多选。
 * @method $this joinValues(bool $value) 设置拼接值
 * @method $this extractValue(bool $value) 设置提取值
 * @method $this delimiter(string $value) 设置拼接符
 * @method $this autoUpload(bool $value) 设置否选择完就自动开始上传
 * @method $this hideUploadButton(bool $value) 设置隐藏上传按钮 
 * @method $this fileField(string $value) 设置如果你不想自己存储，则可以忽略此属性。
 * @method $this crop(bool|array $value) 设置用来设置是否支持裁剪。
 * @method $this limit(array $value) 设置限制图片大小，超出不让上传。
 * @method $this frameImage(string $value) 设置默认占位图地址
 * @method $this fixedSize(bool $value) 设置是否开启固定尺寸,若开启，需同时设置 fixedSizeClassName
 * @method $this fixedSizeClassName(string $value) 设置开启固定尺寸时，根据此值控制展示尺寸。例如h-30,即图片框高为 h-30,AMIS 将自动缩放比率设置默认图所占位置的宽度，最终上传图片根据此尺寸对应缩放。
 * @method $this initAutoFill(bool $value) 设置表单反显时是否执行 autoFill
 * @method $this uploadBtnText(string|array $value) 设置上传按钮文案。支持 tpl、schema 形式配置。
 * @method $this dropCrop(bool $value) 设置图片上传后是否进入裁剪模式
 * @method $this initCrop(bool $value) 设置图片选择器初始化后是否立即进入裁剪模式
 * @method $this draggable(bool $value) 设置开启后支持拖拽排序改变图片值顺序
 * @method $this draggableTip(string $value) 设置拖拽提示文案
 * @method $this className(string $value) 设置 CSS 类名
 */
class InputImage extends FormBase
{
    public string $type = 'input-image';

    private bool $reserved = false;

    /**
     * 构造函数
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function __construct()
    {
        // 设置全局前端上传API
        $this->receiver('${UPLOAD_IMAGE_API}');
    }
}
