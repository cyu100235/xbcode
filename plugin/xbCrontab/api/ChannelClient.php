<?php
/**
 * 积木云渲染器
 * @package  XbCode.0
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCrontab\api;

use Exception;
use Webman\Channel\Client;
use plugin\xbCode\utils\FrameUtil;

/**
 * 跨进程通信客户端
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class ChannelClient
{
    /**
     * 获取Channel服务端口
     * @return int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getPort()
    {
        $port = FrameUtil::xbServerPort() + 1;
        if (!$port) {
            throw new Exception('获取【定时任务】端口号失败');
        }
        return $port;
    }

    /**
     * 初始化Channel服务连接参数
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function init(): array
    {
        // Channel服务ip地址
        $ip = env('CHANNEL_HOST', '127.0.0.1');
        // Channel服务端口
        $port = static::getPort();
        return [
            $ip,
            $port,
        ];
    }

    /**
     * 推送信息到Channel服务
     * @param string $eventName 订阅事件
     * @param array $eventData 事件数据
     * @param array $userIds 给用户ID列表发送信息
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function publish(string $eventName = 'event_websocket', array $eventData = [], array $userIds = [])
    {
        if (empty($eventName)) {
            throw new Exception('事件名称不能为空');
        }
        if (empty($eventData)) {
            throw new Exception('事件数据不能为空');
        }
        $eventData['state'] = $eventData['state'] ?? '10';
        if ($eventData['state'] != '20') {
            return [
                'code' => 400,
                'message' => '事件状态非运行'
            ];
        }
        list($ip, $port) = static::init();
        // 发送的数据
        $sendData = [
            'data' => $eventData, //事件数据
            'userIds' => $userIds //发给用户ID列表
        ];
        // 连接到Channel服务
        Client::connect($ip, $port);
        // 订阅这个事件的客户端会收到事件数据，并触发客户端对应的事件回调
        Client::publish($eventName, $sendData);
        return [
            'code' => 200,
            'message' => '事件发布成功'
        ];
    }

    /**
     * 订阅Channel服务事件
     * @param string $eventName 订阅事件
     * @param callable $callback 回调方法
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function subscribe(string $eventName = 'event_websocket', ?callable $callback = null)
    {
        list($ip, $port) = static::init();
        // 连接到Channel服务
        Client::connect($ip, $port);
        // 订阅某个自定义事件并注册回调，收到事件后会自动触发此回调
        Client::on($eventName, function ($data) use ($callback) {
            $callback($data);
        });
    }

    /**
     * 删除Channel初始服务配置文件
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function deleteConfig()
    {
        $configPath = config_path() . '/plugin/webman/channel/process.php';
        if (!file_exists($configPath)) {
            return;
        }
        unlink($configPath);
    }
}