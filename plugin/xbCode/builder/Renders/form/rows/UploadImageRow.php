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
namespace plugin\xbCode\builder\Renders\form\rows;

use plugin\xbCode\builder\Components\Form\InputImage;

/**
 * 上传图片表单项
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait UploadImageRow
{
    /**
     * 添加上传图片组件
     * @param string $field
     * @param string $title
     * @param mixed $value
     * @param callable|array $option
     * @return InputImage
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowUploadImage(string $field, string $title, mixed $value = '', callable|array $option = []): InputImage
    {
        /** @var InputImage */
        $component = $this->addRow(InputImage::class, $field, $title, $value, $option);
        return $component;
    }
}
