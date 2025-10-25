<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @version  1.0
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\api;

use Exception;

/**
 * 插件预览图接口类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class PluginPreviewApi
{
    /**
     * 单色背景
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private $bgColor = [
        '#7B64FF',
        '#F44E3B',
        '#FB9E00',
        '#68BC00',
        '#16A5A5',
    ];

    /**
     * 渐变背景
     * @var array
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private $gradientColor = [
        ['#7B64FF', '#F44E3B'],
        ['#FB9E00', '#68BC00'],
        ['#16A5A5', '#7B64FF'],
        ['#F44E3B', '#7B64FF'],
    ];

    /**
     * 实例化
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function make()
    {
        return new static;
    }

    /**
     * 创建插件预览图
     * @param array $plugin
     * @return void
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function create(array $plugin)
    {
        $targetPath = base_path() . "/plugin/{$plugin['name']}/preview.svg";
        if (file_exists($targetPath)) {
            return;
        }
        $gradient = $plugin['gradient'] ?? false;
        if ($gradient) {
            // 创建渐变色预览图
            $this->createMulticolorPreview($plugin);
        } else {
            // 创建单色预览图
            $this->createColorPreview($plugin);
        }
    }

    /**
     * 创建单色预览图
     * @param array $plugin
     * @throws Exception
     * @return void
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function createColorPreview(array $plugin)
    {
        $bgColor = $this->getRandBgColor(false);
        if (empty($bgColor)) {
            throw new Exception('获取单色背景失败');
        }
        $targetPath = base_path() . "/plugin/{$plugin['name']}/preview.svg";
        $previewContent = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" width="800" height="600">
<rect width="800" height="600" fill="'.$bgColor.'"></rect>
<text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="monospace" font-size="120" fill="#FFFFFF">
' . $plugin['title'] . '
</text>
</svg>';
        file_put_contents($targetPath, $previewContent);
    }

    /**
     * 创建渐变色预览图
     * @param array $plugin
     * @return void
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function createMulticolorPreview(array $plugin)
    {
        $bgColor = $this->getRandBgColor(true);
        if (!isset($bgColor[0]) || !isset($bgColor[1])) {
            throw new Exception('获取渐变背景色失败');
        }
        $targetPath = base_path() . "/plugin/{$plugin['name']}/preview.svg";
        $previewContent = '<svg xmlns="http://www.w3.org/2000/svg" width="800" height="600">
 <defs>
  <linearGradient spreadMethod="pad" y2="0" x2="1" y1="0" x1="0" id="svg_1">
   <stop offset="0" stop-opacity="0.99609" stop-color="' . $bgColor[0] . '"/>
   <stop offset="1" stop-opacity="0.99219" stop-color="' . $bgColor[1] . '"/>
  </linearGradient>
 </defs>
 <g>
  <title>积木云网络</title>
  <path transform="rotate(130 384.241 302.705)" stroke="#000" id="svg_2" d="m-131.90608,-211.85955l1032.29358,0l0,1029.12868l-1032.29358,0l0,-1029.12868z" opacity="undefined" fill="url(#svg_1)"/>
  <text xml:space="preserve" dominant-baseline="middle" text-anchor="middle" font-family="monospace" font-size="120" stroke-width="0" id="svg_3" x="50%" y="50%"  stroke="#000" fill="#ffffff">' . $plugin['title'] . '</text>
 </g>
</svg>';
        file_put_contents($targetPath, $previewContent);
    }

    /**
     * 获取随机背景色
     * @param bool $gradient 获取随机渐变色，默认false
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function getRandBgColor(bool $gradient)
    {
        if ($gradient) {
            return $this->gradientColor[array_rand($this->gradientColor)];
        }
        return $this->bgColor[array_rand($this->bgColor)];
    }
}