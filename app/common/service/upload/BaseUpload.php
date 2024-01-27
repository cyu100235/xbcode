<?php

namespace app\common\service\upload;

use app\common\model\Upload;
use app\common\model\UploadCate;
use app\common\utils\SettingUtil;
use app\common\validate\AliyunValidate;
use app\common\validate\QcloudValidate;
use app\common\validate\QiniuValidate;
use Exception;
use think\facade\Config;
use yzh52521\filesystem\facade\Filesystem;

/**
 * 附件上传基础类
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
trait BaseUpload
{
    /**
     * 检测文件是否已存在
     * @param mixed $fileName
     * @param mixed $adapter
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getFileInfo($fileName, $adapter)
    {
        $where['filename'] = $fileName;
        $where['adapter']  = $adapter;
        $fileModel = Upload::where($where)->find();
        if ($fileModel) {
            $fileModel->update_at = date('Y-m-d H:i:s');
            $fileModel->save();
            return $fileModel->toArray();
        }
        return [];
    }

    /**
     * 获取储存的分类
     * @param string $dir_name
     * @param mixed $appid
     * @param mixed $store_id
     * @throws \Exception
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected static function getCategory(string $dir_name): array
    {
        $where = [];
        if ($dir_name) {
            $where[] = ['dir_name', '=', $dir_name];
        } else {
            $where[] = ['is_system', '=', '20'];
        }
        $category = UploadCate::where($where)->find();
        if (!$category) {
            $category = UploadCate::order(['id' => 'asc'])->find();
        }
        if (!$category) {
            throw new Exception('没有更多的附件分类可用');
        }
        return $category->toArray();
    }

    /**
     * 获取附件库配置项
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getConfig()
    {
        $active = SettingUtil::config('upload','local','selected_active');
        $config = SettingUtil::config('upload',[]);
        if (empty($config)) {
            throw new Exception('请先设置附件库上传设置');
        }
        // 本地附件库设置
        if ($active === 'local') {
            $web_url = SettingUtil::config('system','','web_url');
            if (empty($web_url)) {
                throw new Exception('请先设置系统域名');
            }
            $config[$active]['url'] = $web_url;
        }
        return [
            'active'        => $active,
            'config'        => $config
        ];
    }

    /**
     * 获取当前使用配置项
     * @param mixed $drive
     * @throws \Exception
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getCurrentConfig($drive = '')
    {
        $config = self::getConfig();
        $active = $config['active'] ?? '';
        // 当前使用驱动
        if (empty($drive)) {
            $drive = $config['active'];
        }
        // 附件库配置
        if (empty($config['config'])) {
            throw new Exception('请先设置附件库上传设置', 13000);
        }
        return $config['config'][$active] ?? [];
    }

    /**
     * 获取当前使用驱动
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getDrive()
    {
        $config = self::getConfig();
        return $config['active'] ?? '';
    }

    /**
     * 获取驱动SDK
     * @param mixed $drive
     * @return \yzh52521\filesystem\Driver
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getDisk($drive = '')
    {
        // 获取配置项
        $config = self::getConfig();
        // 设置驱动
        if (empty($drive)) {
            $drive = $config['active'];
        }
        // 当前使用附件库配置
        $settings = $config['config'][$drive] ?? [];
        // 合并模板配置
        $templateConfig = config("filesystem.disks", []);
        if (!isset($templateConfig[$drive])) {
            $templateConfig[$drive] = [];
        }
        $templateConfig[$drive] = $settings;
        // 阿里云驱动验证
        if ($drive === 'aliyun') {
            xbValidate(AliyunValidate::class, $settings);
        }
        // 腾讯云驱动
        if ($drive === 'qcloud') {
            xbValidate(QcloudValidate::class, $settings);
        }
        // 七牛云驱动
        if ($drive === 'qiniu') {
            xbValidate(QiniuValidate::class, $settings);
        }
        // 动态设置配置
        Config::set([
            'default'   => $drive,
            'disks'     => $templateConfig
        ], 'filesystem');
        // 获取驱动SDK
        return Filesystem::disk($drive);
    }


    /**
     * 保存文件信息
     * @param string $path
     * @param string $dir_name
     * @param string $drive
     * @return Upload
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function addUpload(string $path, string $dir_name, string $drive = 'local')
    {
        // 完整地址
        $fullPath = public_path() . $path;
        // 获取文件信息
        $info = pathinfo($fullPath);
        $size = filesize($fullPath);
        // 获取分类
        $category = self::getCategory($dir_name);
        // 组装数据
        $data = [
            'cid'           => $category['id'],
            'title'         => $info['basename'],
            'filename'      => $info['filename'],
            'format'        => $info['extension'],
            'adapter'       => $drive,
            'size'          => $size,
            'path'          => $path,
        ];
        $model = new Upload;
        if (!$model->save($data)) {
            throw new Exception('保存文件信息失败');
        }
        return $model;
    }
}