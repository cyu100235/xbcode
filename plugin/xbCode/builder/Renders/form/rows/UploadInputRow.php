<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Renders\form\rows;

use plugin\xbCode\builder\Components\Image;
use plugin\xbCode\builder\Components\Button;
use plugin\xbCode\builder\Components\Form\InputFile;
use plugin\xbCode\builder\Components\Form\InputText;
use plugin\xbCode\builder\Components\Form\InputGroup;
use plugin\xbCode\builder\Components\Action\CopyAction;

/**
 * 上传文件组件表单项
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait UploadInputRow
{
    /**
     * 添加上传文件输入框组件表单项
     * @param string $field 字段名
     * @param string $title 描述词
     * @param mixed $value 初始值
     * @param array $uploadOption 上传组件配置
     * @param array $inputOption 输入框组件配置
     * @param array $option 上传配置
     * - `copy` 是否显示复制链接按钮
     * - `reset` 是否显示重置按钮
     * @return InputGroup
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowUploadInputFile(string $field, string $title, mixed $value = '', array $uploadOption = [], array $inputOption = [], array $option = [])
    {
        $component = $this->addRowUploadInput($field, $title, $value, $uploadOption, $inputOption, $option);
        return $component;
    }

    /**
     * 添加上传文件输入框组件表单项
     * @param string $field 字段名
     * @param string $title 描述词
     * @param mixed $value 初始值
     * @param array $uploadOption 上传组件配置
     * @param array $inputOption 输入框组件配置
     * @param array $option 上传配置
     * - `copy` 是否显示复制链接按钮
     * - `reset` 是否显示重置按钮
     * @return InputGroup
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowUploadInputImage(string $field, string $title, mixed $value = '', array $uploadOption = [], array $inputOption = [], array $option = [])
    {
        // 设置上传文件类型为图片
        if (empty($uploadOption['accept'])) {
            $uploadOption['accept'] = '.jpg,.jpeg,.png,.gif,.bmp,.webp';
        }
        $component = $this->addRowUploadInput($field, $title, $value, $uploadOption, $inputOption, $option);
        return $component;
    }

    /**
     * 添加上传输入框组件表单项
     * @param string $field 字段名
     * @param string $title 描述词
     * @param mixed $value 初始值
     * @param array $uploadOption 上传组件配置
     * @param array $inputOption 输入框组件配置
     * @param bool $option 上传配置
     * - `copy` 是否显示复制链接按钮
     * - `reset` 是否显示重置按钮
     * @return InputGroup
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function addRowUploadInput(string $field, string $title, mixed $value = '', array $uploadOption = [], array $inputOption = [], array $option = [])
    {
        /** @var InputGroup */
        $component = $this->addRow(InputGroup::class, $field, $title, $value);
        $component->className('xb-hide-upload-file-list');
        // 输入框组件ID（重置按钮通过 componentId 定位目标，target 方式在 amis 6.13.0 下无效）
        $inputId = 'xb-reset-' . md5($field);
        // 左侧输入框组件（未上传时可编辑，上传完成有值后禁用）
        $inputComponent = InputText::make()
            ->name($field)
            ->id($inputId)
            ->label($title)
            ->setVariables($inputOption)
            ->disabledOn('!!this.' . $field)
            ->placeholder('请选择文件上传');
        // 右侧上传组件（组合框内不渲染 label，需用 btnLabel 控制上传按钮文字）
        $uploadComponent = InputFile::make()
            ->btnLabel('上传')
            ->accept('*')
            ->maxLength(1)
            ->setVariables($uploadOption)
            ->autoFill([
                $field => '${url}'
            ]);
        // 输入框组合内部的 input-text 不支持 addOn，复制按钮需作为组合框的兄弟节点
        $body = [$inputComponent->get()];
        // 图片类型：预览按钮 + 隐藏图片（隐藏图片作为预览数据源，触发 amis 原生放大预览层）
        $isImage = $this->isUploadImageFile($uploadOption);
        $previewClass = 'xb-preview-image-' . md5($field);
        if ($isImage) {
            $body[] = Button::make()
                ->label('预览')
                ->className('text-success')
                // 未上传时禁用预览按钮
                ->disabledOn('!this.' . $field)
                ->onEvent([
                    'click' => [
                        'actions' => [
                            [
                                'actionType' => 'custom',
                                'script' => $this->buildPreviewScript($previewClass),
                            ]
                        ],
                    ],
                ])
                ->get();
            $body[] = Image::make()
                ->src('${' . $field . '}')
                ->enlargeAble(true)
                ->className($previewClass)
                ->style([
                    'position' => 'absolute',
                    'top' => 0,
                    'left' => 0,
                    'width' => '1px',
                    'height' => '1px',
                    'opacity' => 0,
                    'overflow' => 'hidden',
                    'pointer-events' => 'none',
                    'z-index' => -1,
                ])
                ->get();
        }
        // 是否显示复制链接按钮
        $copyable = $option['copy'] ?? true;
        if ($copyable) {
            $body[] = CopyAction::make()
                ->label('复制')
                ->className('text-warning')
                // 未上传时禁用复制按钮
                ->disabledOn('!this.' . $field)
                ->content('${' . $field . '}')
                ->get();
        }
        // 是否显示重置按钮
        $reset = $option['reset'] ?? true;
        // 清空目标需用 componentId 定位（target 传字段名在 amis 6.13.0 下不生效）
        if ($reset) {
            $body[] = Button::make()
                ->label('重置')
                ->className('text-danger')
                ->disabledOn('!this.' . $field)
                ->onEvent([
                    'click' => [
                        'actions' => [
                            [
                                'actionType' => 'clear',
                                'componentId' => $inputId,
                            ]
                        ],
                    ],
                ])
                ->get();
        }
        $body[] = $uploadComponent->get();
        $component->body($body);
        return $component;
    }

    /**
     * 构建图片预览按钮的点击脚本，触发隐藏图片的原生放大预览层
     * @param string $previewClass 预览图片的 CSS 类名
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function buildPreviewScript(string $previewClass): string
    {
        return "(function(){var r=document.querySelector('." . $previewClass . "');if(!r)return;var t=r.querySelector('.cxd-Image-overlay-view-icon');if(!t)t=r.querySelector('img');if(t)t.click();})()";
    }

    /**
     * 判断上传配置是否为图片类型
     * @param array $uploadOption 上传组件配置
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function isUploadImageFile(array $uploadOption): bool
    {
        $accept = (string) ($uploadOption['accept'] ?? '');
        if ($accept === '' || $accept === '*') {
            return false;
        }
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'ico', 'avif'];
        foreach (explode(',', $accept) as $item) {
            $extension = strtolower(ltrim(trim($item), '.'));
            if (in_array($extension, $imageExtensions, true)) {
                return true;
            }
        }
        return false;
    }
}
