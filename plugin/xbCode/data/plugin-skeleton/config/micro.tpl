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

use plugin\xbCode\api\ServerToken;

return [
    // 服务地址（含协议与端口）
    'server_address' => '{PLUGIN_SERVER_ADDRESS}',
    // 互通令牌：只读主项目
    'token' => ServerToken::get(),
];
