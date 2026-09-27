<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\app\model;

use plugin\xbCode\app\Model;
use plugin\xbAdmin\api\Files;

/**
 * 附件模型
 *
 * 记录每个上传文件的归属人（uid 为 0 表示后台上传）、适配器、指纹与存放地址，
 * url / size_format 为访问器附加字段，供后台列表与接口直接输出。
 */
class Upload extends Model
{
    /**
     * 模型输出附加字段
     * @var array
     */
    protected $append = [
        'url',
        'size_format',
    ];

    /**
     * 追加文件访问地址
     * @param mixed $value
     * @param array $data
     * @return string
     */
    protected function getUrlAttr($value, $data)
    {
        return Files::make()->url((string) ($data['uri'] ?? ''), (string) ($data['adapter'] ?? ''));
    }

    /**
     * 追加文件大小格式化
     * @param mixed $value
     * @param array $data
     * @return string
     */
    protected function getSizeFormatAttr($value, $data)
    {
        return get_size((int) ($data['size'] ?? 0));
    }
}