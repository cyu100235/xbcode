<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\bootstrap;

use Throwable;
use Webman\Config;

/**
 * 缓存环境隔离
 *
 * 同一台机器上可能同时运行多个环境（本地 / 测试 / 生产），它们共用同一个 Redis 库。
 * 若各环境的键不加区分，一个环境会读到、甚至取走另一个环境的数据
 * —— 曾因此把生产数据写进测试库，表现为「统计值总是对不上」。
 *
 * 做法：在进程启动阶段把「实例端口」注入各缓存配置的 prefix，
 * 使每个环境的键天然落在自己的命名空间内，形如 xb_39200:。
 *
 * 覆盖内容放在插件数据目录 data/cache-isolation，只声明需要覆盖的配置项，
 * 项目根目录的 config 目录保持框架原样（与 WebmanLogFilter 同一思路）。
 * 注入时机早于任何 Redis / 缓存 / 会话连接的创建，因此对全部进程生效。
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class CacheIsolation
{
    /**
     * 应用缓存隔离
     *
     * 目录下的配置文件覆盖以下根级键（配置文件在载入时自行计算命名空间）：
     * - redis.php         → support\Redis 连接（含 Lua EVAL 的 KEYS）
     * - think-cache.php   → ThinkPHP 缓存（含 tag: 键）
     * - session.php       → 会话（redis / redis_cluster 变体）
     * - redis_queue.php   → redis-queue 队列名，优先级高于 plugin.webman.redis-queue.redis
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function apply(): void
    {
        $dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'cache-isolation';
        if (!is_dir($dir)) {
            return;
        }
        try {
            // 按根级键合并：目录下的文件名即一级配置键；
            // app.php 仅为满足框架「同目录须有 app.php」的扫描前提，用 exclude 跳过
            Config::load($dir, ['app']);
        } catch (Throwable $e) {
            // 隔离属于部署增强，异常时退回框架默认配置，不阻断进程启动
        }
    }
}
