<?php
/**
 * 插件进程配置
 * @package  XbCode
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 *
 * 本插件不启动独立进程：后台接口与页面复用 xbCode 插件的 HTTP 进程，
 * 通过跨插件路由 /xbAdmin/admin/{控制器}/{方法} 访问，故此处保持空数组。
 */

return [];
