<?php
namespace app\providers\upload;

use app\providers\ConfigProvider;

class Config
{
    /**
     * 平台对应产品
     * @var array
     */
    public static $platform = [
        'aliyun'        => 'oss',
        'qcloud'        => 'cos',
        'qiniu'         => 'qiniu',
    ];
    
    /**
     * 获取默认配置项
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getDefaultConfig(){
        return [
            'default' => 'public',
            'max_size' => 1024*1024*100,
            'ext_yes' => '',
            'ext_no' => '',
            'storage' => [
                'public' => ['path'=>'/uploads','url'=>'http://'.\request()->host().'/uploads'],
                'local' => ['path'=>'','url'=>'http://'.\request()->host()],
                'oss' => ['access_id'=>'','access_key' => '','bucket' => '','endpoint' => '','ssl' => true,'isCName' => true,'url'=>'http://'.\request()->host()],
                's3'=>[
                    'key' => '',
                    'secret' => '',
                    'region' => '',
                    'version' => '',
                    'bucket_endpoint' => false,
                    'use_path_style_endpoint' => false,
                    'endpoint' => '',
                    'bucket_name' => '',
                    'url'=> 'http://'.\request()->host()
                ],
                'cos'=>[
                    'region'=> '',
                    'app_id' => '',
                    'secret_id' => '',
                    'secret_key' => '',
                    'bucket' => '',
                    'signed_url' => false,
                    'read_from_cdn' => false,
                    'url'=> 'http://'.\request()->host()
                ],
                'qiniu'=>[
                    'accessKey'=> '',
                    'secretKey' => '',
                    'bucket' => '',
                    'domain' => '',
                    'url'=> 'http://'.\request()->host()
                ],
                'ftp'=>[
                    'host'=> '',
                    'username' => '',
                    'password' => '',
                    'port' => 21,
                    'root' => '',
                    'passive' => false,
                    'ssl' => false,
                    'url'=> 'http://'.\request()->host()
                ]

            ]
        ];
    }

    /**
     * 获取数据库配置
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getDbConfig(){
        // 获取配置
        $config = ConfigProvider::get('upload','',self::getDefaultConfig());
        // 格式化配置
        $data = [
            'default' => $config['active']??'public',
            'max_size' => $config['max_size'] ?? 1024 * 1024 * 100, //单个文件大小100M
            'ext_yes' => empty($config['ext_yes']) ? []: explode(',',$config['ext_yes']), //允许上传文件类型 为空则为允许所有
            'ext_no' => empty($config['ext_no']) ? []: explode(',',$config['ext_no']), // 不允许上传文件类型 为空则不限制
            'storage' => [
                // 本地（开放）
                'public' => [
                    'driver' => \Shopwwi\WebmanFilesystem\Adapter\LocalAdapterFactory::class,
                    'root' => public_path(),
                    'path' => $config['public']['path'] ?? '/uploads',
                    'url' => $config['public']['url'] ?? '' // 静态文件访问域名
                ],
                // 本地（私有）
                'local' => [
                    'driver' => \Shopwwi\WebmanFilesystem\Adapter\LocalAdapterFactory::class,
                    'root' => runtime_path().($config['local']['root'] ?? '/uploads'),
                    'url' => $config['local']['url'] ?? '' // 静态文件访问域名
                ],
                // FTP
                'ftp' => [
                    'driver' => \Shopwwi\WebmanFilesystem\Adapter\FtpAdapterFactory::class,
                    'host' => $config['ftp']['host'] ?? '',
                    'username' => $config['ftp']['username'] ?? '',
                    'password' => $config['ftp']['password'] ?? '',
                    'url' => $config['ftp']['url'] ?? '', // 静态文件访问域名
                    'port' => $config['ftp']['port'] ?? 21,
                    'root' => $config['ftp']['root'] ?? '',
                    'passive' => $config['ftp']['passive'] ?? false,
                    'ssl' => $config['ftp']['ssl'] ?? false,
                    // 'timeout' => 30,
                    // 'ignorePassiveAddress' => false,
                    // 'timestampsOnUnixListingsEnabled' => true,
                ],
                'memory' => [
                    'driver' => \Shopwwi\WebmanFilesystem\Adapter\MemoryAdapterFactory::class,
                ],
                's3' => [
                    'driver' => \Shopwwi\WebmanFilesystem\Adapter\S3AdapterFactory::class,
                    'credentials' => [
                        'key' => $config['s3']['key'] ?? '',
                        'secret' => $config['s3']['secret'] ?? '',
                    ],
                    'region' => $config['s3']['region'] ?? '',
                    'version' => $config['s3']['version'] ?? '',
                    'bucket_endpoint' => $config['s3']['bucket_endpoint'] ?? false,
                    'use_path_style_endpoint' => $config['s3']['use_path_style_endpoint'] ?? false,
                    'endpoint' => $config['s3']['endpoint'] ?? '',
                    'bucket_name' => $config['s3']['bucket_name'] ?? '',
                    'url' => '' // 静态文件访问域名
                ],
                // 阿里云配置
                'oss' => [
                    'driver' => \Shopwwi\WebmanFilesystem\Adapter\AliyunOssAdapterFactory::class,
                    'accessId' => $config['oss']['accessId'] ?? '',
                    'accessSecret' => $config['oss']['accessSecret'] ?? '',
                    'bucket' => $config['oss']['bucket'] ?? '',
                    'endpoint' => $config['oss']['endpoint'] ?? '',
                    'url' => $config['oss']['url'] ?? '', // 静态文件访问域名
                    // 'timeout' => 3600,
                    // 'connectTimeout' => 10,
                    'isCName' => $config['oss']['isCName'] ?? false,
                    // 'token' => null,
                    // 'proxy' => null,
                ],
                // 七牛云配置
                'qiniu' => [
                    'driver' => \Shopwwi\WebmanFilesystem\Adapter\QiniuAdapterFactory::class,
                    'accessKey' => $config['qiniu']['accessKey'] ?? '',
                    'secretKey' => $config['qiniu']['secretKey'] ?? '',
                    'bucket' => $config['qiniu']['bucket'] ?? '',
                    'domain' => $config['qiniu']['domain'] ?? '',
                    'url' => $config['qiniu']['url'] ?? '' // 静态文件访问域名
                ],
                // 腾讯云配置
                'cos' => [
                    'driver' => \Shopwwi\WebmanFilesystem\Adapter\CosAdapterFactory::class,
                    'region' => $config['qcloud']['region'] ?? '',
                    'app_id' => $config['qcloud']['app_id'] ?? '',
                    'secret_id' => $config['qcloud']['secret_id'] ?? '',
                    'secret_key' => $config['qcloud']['secret_key'] ?? '',
                    // 可选，如果 bucket 为私有访问请打开此项
                    'signed_url' => $config['qcloud']['private_type'] ?? false,
                    'bucket' => $config['qcloud']['bucket'] ?? '',
                    'read_from_cdn' => $config['qcloud']['read_from_cdn'] ?? false,
                    'url' => $config['qcloud']['url'] ?? '' // 静态文件访问域名
                    // 'timeout' => 60,
                    // 'connect_timeout' => 60,
                    // 'cdn' => '',
                    // 'scheme' => 'https',
                ],
            ],
        ];
        // 返回配置
        return $data;
    }
}