<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbUploadOss\api;

use plugin\xbCode\base\BasePlugin;
use plugin\xbAdmin\api\EngineApi;

/**
 * 插件安装器
 * @copyright 贵州积木云网络科技有限公司
 * @author    楚羽幽 958416459@qq.com
 */
class Install extends BasePlugin
{
    /**
     * 云存储引擎类型
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static $engine = 'aliyun';

    /**
     * 安装之后，登记储存引擎记录
     * @param string $version
     * @param array|null $context
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function afterInstall(string $version, array|null &$context = null)
    {
        $desc = '<div>存储在阿里云，请前往阿里云开通存储服务 <a href="https://www.aliyun.com/product/oss" target="_blank">立即去开通</a></div>';
        EngineApi::make()->add([
            'title' => '阿里云OSS',
            'name' => static::$engine,
            'plugin' => (string) ($context['name'] ?? ''),
            'desc' => $desc,
            'prompt' => '切换阿里云OSS后，素材库需要重新上传至阿里云OSS',
        ]);
    }

    /**
     * 卸载之后，删除储存引擎记录
     * @param string $version
     * @param array $context
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function afterUninstall(string $version, array &$context)
    {
        EngineApi::make()->del((string) ($context['name'] ?? ''), static::$engine);
    }
}