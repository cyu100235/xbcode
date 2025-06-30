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
    /**
     * 图片类型
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    const IMAGE = 'jpg,jpeg,png,gif';

    /**
     * 视频类型
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    const VIDEO = 'mp4,avi,rmvb,mkv,flv';

    /**
     * 文档类型
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    const DOC = 'doc,docx,xls,xlsx,ppt,pptx,pdf,txt,pem';

    /**
     * 音频类型
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    const AUDIO = 'mp3,wav,flac,ape,alac';

    /**
     * 字体类型
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    const FONT = 'ttf,otf,woff,woff2,eot';

    /**
     * 压缩包类型
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    const ZIP = 'zip,rar,7z,tar,gz,bz2';

    /**
     * 其他类型
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    const OTHER = '*';
}