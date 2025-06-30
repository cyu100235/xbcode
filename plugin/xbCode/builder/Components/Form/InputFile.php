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
 * 文件上传组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-file
 * @method $this receiver(string $value) 上传文件接口
 * @method $this accept(string $value) 设置支持的文件类型
 * @method $this capture(string $value) 设置移动端输入来源
 * @method $this asBase64(bool $value) 是否将文件以base64的形式赋值
 * @method $this asBlob(bool $value) 是否将文件以二进制的形式赋值
 * @method $this maxSize(int $value) 设置文件大小限制，单位为B
 * @method $this maxLength(int $value) 设置一次上传的文件数量限制
 * @method $this multiple(bool $value) 是否允许多选
 * @method $this drag(bool $value) 是否启用拖拽上传
 * @method $this joinValues(bool $value) 是否拼接值
 * @method $this extractValue(bool $value) 是否提取值
 * @method $this delimiter(string $value) 设置拼接符
 * @method $this autoUpload(bool $value) 是否选择完就自动开始上传
 * @method $this hideUploadButton(bool $value) 是否隐藏上传按钮
 * @method $this stateTextMap(array $value) 设置上传状态文案
 * @method $this fileField(string $value) 设置文件字段名
 * @method $this nameField(string $value) 设置文件名字段名
 * @method $this valueField(string $value) 设置文件值字段名
 * @method $this urlField(string $value) 设置文件下载地址字段名
 * @method $this btnLabel(string $value) 设置上传按钮文字
 * @method $this downloadUrl(bool|string $value) 设置是否支持直接下载文件
 * @method $this useChunk(bool|string $value) 是否使用分块上传
 * @method $this chunkSize(int $value) 设置分块大小，默认5MB
 * @method $this startChunkApi(string $value) 设置分块上传开始接口
 * @method $this chunkApi(string $value) 设置分块上传接口
 * @method $this finishChunkApi(string $value) 设置分块上传完成接口
 * @method $this concurrency(int $value) 设置分块上传时的并行个数
 * @method $this documentation(string $value) 设置文档内容
 * @method $this documentLink(string $value) 设置文档链接
 * @method $this initAutoFill(bool $value) 设置初表单反显时是否执行
 */
class InputFile extends FormBase
{
    public string $type = 'input-file';    

    /**
     * 构造函数
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function __construct()
    {
        // 设置全局前端上传API
        $this->receiver('${UPLOAD_FILE_API}');
    }
}
