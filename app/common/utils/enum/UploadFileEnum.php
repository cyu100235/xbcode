<?php

namespace app\common\utils\enum;

use app\common\Enum;

/**
 * 附件类型
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class UploadFileEnum extends Enum
{
    const IMAGE = [
        'label'         => '图片类型',
        'value'         => 'image',
        'format'        => ['jpg','jpeg','png','gif'],
    ];
    const VIDEO = [
        'label'         => '视频类型',
        'value'         => 'image',
        'format'        => ['mp4','avi','rmvb','mkv','flv'],
    ];
    const DOC = [
        'label'         => '文档类型',
        'value'         => 'doc',
        'format'        => ['doc','docx','xls','xlsx','ppt','pptx','pdf','txt'],
    ];
    const AUDIO = [
        'label'         => '音频类型',
        'value'         => 'audio',
        'format'        => ['mp3','wav','flac','ape','alac'],
    ];
    const TTF = [
        'label'         => '字体类型',
        'value'         => 'ttf',
        'format'        => ['ttf','otf','woff','woff2'],
    ];
    const ZIP = [
        'label'         => '压缩包类',
        'value'         => 'zip',
        'format'        => ['zip','rar','7z','tar','gz','bz2'],
    ];
}
