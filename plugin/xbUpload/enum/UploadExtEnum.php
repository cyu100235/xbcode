<?php
namespace plugin\xbUpload\enum;

use plugin\xbCode\base\BaseEnum;

/**
 * 上传附件枚举
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class UploadExtEnum extends BaseEnum
{
    const IMAGE = [
        'label' => '图片',
        'value' => 'image',
        'ext' => 'jpg,jpeg,png,gif,svg,webp,heif,raw,jfif',
        'style' => 'success',
    ];
    const VIDEO = [
        'label'=> '视频',
        'value' => 'video',
        'ext' => 'mp4,avi,rmvb,mkv,flv',
        'style' => 'info',
    ];
    const DOC = [
        'label' => '文档',
        'value'=> 'doc',
        'ext' => 'doc,docx,xls,xlsx,ppt,pptx,pdf,txt,pem',
        'style' => 'primary',
    ];
    const AUDIO = [
        'label' => '音频',
        'value' => 'audio',
        'ext' => 'mp3,wav,flac,ape,alac',
        'style' => 'warning',
    ];
    const FONT = [
        'label' => '字体',
        'value' => 'font',
        'ext' => 'ttf,otf,woff,woff2,eot',
        'style' => 'danger',
    ];
    const ZIP = [
        'label' => '压缩',
        'value' => 'zip',
        'ext' => 'zip,rar,7z,tar,gz,bz2',
        'style' => 'secondary',
    ];
    const OTHER = [
        'label' => '其他',
        'value' => 'other',
        'ext' => '',
        'style' => 'light',
    ];
}