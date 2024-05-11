<?php

namespace app\providers\upload;

use app\utils\enum\UploadFileEnum;
use app\providers\ConfigProvider;
use app\model\Upload;
use Exception;

/**
 * 附件上传基础类
 * @author 贵州小白基地网络科技有限公司
 * @copyright 贵州小白基地网络科技有限公司
 * @email cy958416459@qq.com
 */
trait BaseUpload
{
    /**
     * 获取文件信息
     * @param array $where
     * @param string $adapter
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getFileInfo(array $where, string $adapter = '')
    {
        if (!$adapter) {
            $adapter = ConfigProvider::get('upload', 'selected_active');
        }
        $where['adapter']  = $adapter;
        $model = Upload::where($where)->find();
        if ($model) {
            $model->update_at = date('Y-m-d H:i:s');
            $model->save();
            $data = $model->toArray();
            return $data;
        }
        return [];
    }
    
    /**
     * 获取储存目录
     * @param string $extension
     * @return string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function getUploadDir(string $extension)
    {
        $uploadEnum = UploadFileEnum::toArray();
        $dateTime   = date('Ymd');
        foreach ($uploadEnum as $value) {
            if (in_array($extension, $value['format'])) {
                return "uploads/{$value['value']}/{$dateTime}";
            }
        }
        return '';
    }
    
    /**
     * 获取附件库配置项
     * @throws \Exception
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getConfig()
    {
        $config = ConfigProvider::get('upload','',[]);
        $active = $config['selected_active'] ?? 'local';
        $settings = $config[$active] ?? [];
        if (empty($settings)) {
            throw new Exception('请先设置附件库上传设置');
        }
        return [
            'active'        => $active,
            'config'        => $config
        ];
    }

    /**
     * 保存文件信息
     * @param array $data
     * @throws \Exception
     * @return Upload
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function addFileInfo(array $data)
    {
        if (empty($data['title'])) {
            throw new Exception('请先设置附件标题');
        }
        if (empty($data['filename'])) {
            throw new Exception('请先设置附件文件名');
        }
        if (empty($data['md5'])) {
            throw new Exception('请先设置文件指纹');
        }
        if (empty($data['format'])) {
            throw new Exception('请先设置附件格式');
        }
        if (empty($data['adapter'])) {
            $data['adapter'] = Config::getDbConfig()['default'] ?? 'public';
        }
        if (empty($data['size'])) {
            throw new Exception('请先设置附件大小');
        }
        if (empty($data['path'])) {
            throw new Exception('请先设置附件路径');
        }
        if (empty($data['uid'])) {
            $data['uid'] = 0;
        }
        $model = new Upload;
        if (!$model->save($data)) {
            throw new Exception('保存文件信息失败');
        }
        return $model;
    }

    /**
     * 保存文件信息
     * @param string $path
     * @param string $dir_name
     * @param string $drive
     * @return Upload
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public static function addUpload(string $path, string $dir_name, string $drive = 'local')
    {
        // 完整地址
        $fullPath = public_path() . $path;
        // 获取文件信息
        $info = pathinfo($fullPath);
        $size = filesize($fullPath);
        // 组装数据
        $data = [
            'dir_name'      => $dir_name,
            'title'         => $info['basename'],
            'filename'      => $info['filename'],
            'format'        => $info['extension'],
            'adapter'       => $drive,
            'size'          => $size,
            'path'          => $path,
        ];
        return self::addFileInfo($data);
    }
}