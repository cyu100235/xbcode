<?php
namespace xbcode\providers\upload;

use xbcode\providers\DictProvider;

/**
 * 上传辅助类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait UploadTrait
{
    /**
     * 获取上传文件储存路径
     * @param string $extension
     * @return string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function getUploadPath(string $extension)
    {
        $name = self::getDictDirName($extension);
        return "uploads/{$name}/" . date('Ymd');
    }

    /**
     * 获取上传文件储存目录名
     * @param string $extension
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function getDictDirName(string $extension)
    {
        $uploadFormat = DictProvider::get('uploadFileFormat')->dict();
        foreach ($uploadFormat as $name => $value) {
            $format = explode(',', $value);
            if (in_array($extension, $format)) {
                return $name;
            }
        }
        return 'other';
    }
}