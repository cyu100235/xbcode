<?php

namespace plugin\xbUpload\app\model;

use plugin\xbCode\Model;
use plugin\xbUpload\api\Files;

/**
 * 附件模型
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class Upload extends Model
{
    // 模型输出字段
    protected $append = [
        'url',
        'size_format',
    ];

    /**
     * 追加URL参数
     * @param mixed $value
     * @param mixed $data
     * @return string
     * @copyright 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-04-30
     */
    protected function getUrlAttr($value, $data)
    {
        return Files::url((string) $data['uri']);
    }

    /**
     * 追加文件大小格式化
     * @param mixed $value
     * @param mixed $data
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function getSizeFormatAttr($value, $data)
    {
        return get_size((int) $data['size']);
    }
}
