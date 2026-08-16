<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Renders\form\rows;

use plugin\xbCode\builder\Components\Form\InputFile;
use plugin\xbCode\builder\Components\Form\InputText;
use plugin\xbCode\builder\Components\Form\InputGroup;

/**
 * 附件选择表单项
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait UploadInputFileRow
{
    /**
     * 添加组合框上传
     * @param string $field 字段名
     * @param string $title 描述词
     * @param mixed $value 初始值
     * @param array $option 上传组件配置
     * @return InputGroup
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowUploadInputFile(string $field, string $title, mixed $value = '', array $option = [])
    {
        /** @var InputGroup */
        $component = $this->addRow(InputGroup::class, $field, $title, $value);
        $component->className('xb-hide-upload-file-list');
        $component->body([
            InputText::make()
                ->name($field)
                ->label($title)
                ->disabled(true)
                ->placeholder('请选择文件上传')
                ->get(),
            InputFile::make()
                ->accept('*')
                ->maxLength(1)
                ->setVariables($option)
                ->autoFill([
                    $field => '${url}'
                ])
                ->get(),
        ]);
        return $component;
    }
}
