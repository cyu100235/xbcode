<?php

namespace app\model;

use xbcode\Model;
use xbcode\providers\ConfigProvider;
use xbcode\providers\FileProvider;

class Upload extends Model
{
    // 模型输出字段
    protected $append = [
        'url',
        'size_format',
    ];

    /**
     * 附件存储引擎
     * @var array
     */
    public static $engineList = [
        [
            'title' => '本地存储',
            'desc' => '存储在本地服务器',
            'prompt' => '本地存储方式不需要配置其他参数',
            'engine' => 'local',
        ],
        [
            'title' => '阿里云OSS',
            'desc' => '存储在阿里云，请前往阿里云开通存储服务',
            'prompt' => '切换阿里云OSS后，素材库需要重新上传至阿里云OSS',
            'engine' => 'aliyun',
        ],
        [
            'title' => '腾讯云COS',
            'desc' => '存储在腾讯云，请前往腾讯云开通存储服务',
            'prompt' => '切换阿里云OSS后，素材库需要重新上传至阿里云OSS',
            'engine' => 'qcloud',
        ],
        [
            'title' => '七牛云存储',
            'desc' => '存储在七牛云，请前往七牛云开通存储服务',
            'prompt' => '切换七牛云存储后，素材库需要重新上传至七牛云',
            'engine' => 'qiniu',
        ],
    ];

    /**
     * 获取存储引擎列表
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getEngineList()
    {
        $appid = request()->saasAppid;
        $default = ConfigProvider::get('upload', 'active', 'local');
        $data    = Upload::$engineList;
        if ($appid) {
            // 本地储存权限
            $local = WebSite::where('id', $appid)->value('local', '10');
            // 无本地存储权限
            if ($local !== '20') {
                $data = array_filter($data, function ($item) {
                    return $item['engine'] !== 'local';
                });
                // 重置索引
                $data = array_values($data);
            }
        }
        $data    = array_map(function ($item) use ($default) {
            $item['state'] = $item['engine'] === $default ? '20' : '10';
            return $item;
        }, $data);
        return $data;
    }

    /**
     * 获取存储引擎字典列表
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getDict()
    {
        return array_column(self::$engineList, 'title', 'engine');
    }

    /**
     * 获取存储引擎详情
     * @param string $name
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getEngineDetail(string $name)
    {
        $engine = array_column(self::$engineList, null, 'engine');
        $data = $engine[$name] ?? [];
        if ($data) {
            $active = ConfigProvider::get('upload', 'active', 'local');
            $data['state'] = $data['engine'] === $active ? '20' : '10';
        }
        return $data;
    }

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
        return FileProvider::url((string) $data['uri']);
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
