<?php

namespace app\model;

use app\common\Model;
use app\common\providers\UploadProvider;

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
        return UploadProvider::url((string) $data['path']);
    }
}
