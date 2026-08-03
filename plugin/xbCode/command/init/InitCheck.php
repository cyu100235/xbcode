<?php
/**
 * 环境检查
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\command\init;

use Exception;

/**
 * 环境检查
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class InitCheck
{
    /**
     * 检查禁用函数
     * @throws Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function checkDisabledFunction(): void
    {
        $disabledPath = base_path('plugin/xbCode/app/install/config/fun.php');
        if (!file_exists($disabledPath)) {
            throw new Exception('禁用函数配置文件不存在');
        }
        $data = include $disabledPath;
        $disabled = [];
        foreach ($data as $value) {
            if (!$value['name']) {
                throw new Exception('禁用函数配置错误');
            }
            if (!function_exists($value['name'])) {
                $disabled[] = $value['name'];
            }
        }
        if ($disabled) {
            $disabled = implode(',', $disabled);
            throw new Exception("【{$disabled}】 函数被禁用，请解除函数禁用");
        }
    }

    /**
     * 检查扩展
     * @throws Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function checkExtension(): void
    {
        $extraPath = base_path('plugin/xbCode/app/install/config/extra.php');
        if (!file_exists($extraPath)) {
            throw new Exception('扩展配置文件不存在');
        }
        $data = include $extraPath;
        $list = [];
        foreach ($data as $value) {
            if (!$value['name']) {
                throw new Exception('扩展标识配置错误');
            }
            if (!extension_loaded($value['name'])) {
                $list[] = $value['name'];
            }
        }
        if ($list) {
            $extras = implode(',', $list);
            throw new Exception("【{$extras}】 扩展未安装，请安装扩展");
        }
    }
}
