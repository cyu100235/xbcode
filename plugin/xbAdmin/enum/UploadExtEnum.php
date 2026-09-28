<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbAdmin\enum;

use plugin\xbCode\base\BaseEnum;

/**
 * 上传附件枚举
 * @copyright 贵州云铺网络科技有限公司
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
        'label' => '视频',
        'value' => 'video',
        'ext' => 'mp4,avi,rmvb,mkv,flv',
        'style' => 'info',
    ];
    const DOC = [
        'label' => '文档',
        'value' => 'doc',
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

    /**
     * 获取文件后缀与分类目录的映射表（后缀 => 分类标识）
     *
     * 字典只维护 label/style 等展示字段，安装入库时会丢失 ext 字段，
     * 因此文件归类必须直接读取枚举常量，避免后缀信息被字典覆盖后全部落入 other。
     * @return array
     */
    public static function extensionMap(): array
    {
        $map = [];
        foreach (self::constList() as $item) {
            foreach (self::extList($item) as $ext) {
                $map[$ext] = (string) $item['value'];
            }
        }
        return $map;
    }

    /**
     * 获取指定分类包含的文件后缀
     * @param string $value 分类标识
     * @return array
     */
    public static function extensions(string $value): array
    {
        foreach (self::constList() as $item) {
            if ((string) $item['value'] === $value) {
                return self::extList($item);
            }
        }
        return [];
    }

    /**
     * 读取枚举常量定义
     * @return array
     */
    private static function constList(): array
    {
        $list = [];
        foreach ((new \ReflectionClass(self::class))->getConstants() as $key => $item) {
            if (!is_array($item) || !isset($item['value'])) {
                continue;
            }
            $item['key'] = $key;
            $list[] = $item;
        }
        return $list;
    }

    /**
     * 解析常量中的 ext 字段为小写后缀列表
     * @param array $item 枚举常量
     * @return array
     */
    private static function extList(array $item): array
    {
        $exts = array_filter(explode(',', (string) ($item['ext'] ?? '')));
        return array_values(array_map('strtolower', array_map('trim', $exts)));
    }
}