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
namespace plugin\xbCode\builder\Renders\Form\rows;

use plugin\xbCode\builder\Components\Form\InputFile;
use plugin\xbCode\builder\Components\Form\InputImage;
use plugin\xbCode\builder\Components\Form\InputText;

/**
 * 表单项-上传型
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait FormRowUpload
{
    /**
     * 添加上传图片组件
     * @param string $field
     * @param string $title
     * @param string $value
     * @param callable|array $option
     * @return InputImage
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowUploadImage(string $field, string $title, string $value = '', callable|array $option = []): InputImage
    {
        /** @var InputImage */
        $component = $this->addRow(InputImage::class, $field, $title, $value, $option);
        return $component;
    }

    /**
     * 添加上传音频组件
     * @param string $field
     * @param string $title
     * @param string $value
     * @param callable|array $option
     * @return InputFile
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowUploadAudio(string $field, string $title, string $value = '', callable|array $option = []): InputFile
    {
        /** @var InputFile */
        $component = $this->addRow(InputFile::class, $field, $title, $value, $option);
        return $component;
    }

    /**
     * 添加上传视频组件
     * @param string $field
     * @param string $title
     * @param string $value
     * @param callable|array $option
     * @return InputFile
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowUploadVideo(string $field, string $title, string $value = '', callable|array $option = []): InputFile
    {
        /** @var InputFile */
        $component = $this->addRow(InputFile::class, $field, $title, $value, $option);
        return $component;
    }

    /**
     * 添加上传文件组件
     * @param string $field
     * @param string $title
     * @param string $value
     * @param callable|array $option
     * @return InputFile
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowUploadFile(string $field, string $title, string $value = '', callable|array $option = []): InputFile
    {
        /** @var InputFile */
        $component = $this->addRow(InputFile::class, $field, $title, $value, $option);
        return $component;
    }
}
