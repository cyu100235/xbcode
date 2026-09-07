<?php
/**
 * 插件微服务配置
 * @package  XbCode
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */

return [
    // 服务地址（含协议与端口），由 xb:plugin:create 创建插件时采集写入
    'server_address' => '{PLUGIN_SERVER_ADDRESS}',

    // 互通令牌：动态读取主项目 runtime/xbCode/server_token，避免写死在配置里过期
    'token' => trim((string) @file_get_contents(dirname(__DIR__, 3) . '/runtime/xbCode/server_token')),
];
