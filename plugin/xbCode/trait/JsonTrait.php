<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\trait;

use Exception;
use support\Response;

/**
 * JSON构造类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait JsonTrait
{
    /**
     * 返回JSON数据
     * @param mixed $msg
     * @param mixed $status
     * @param mixed $data
     * @param mixed $option
     * @return Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function json(mixed $msg, mixed $status, $data = [], array $option = [])
    {
        $json['msg'] = $msg;
        $json['status'] = $status;
        if (!empty($data)) {
            $json['data'] = $data;
        }
        $json['option'] = $option;
        // 返回JSON
        return json($json);
    }

    /**
     * 返回固定JSON
     * @param array $data
     * @param array $option
     * @throws \Exception
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function response(array $data, array $option = [])
    {
        if (!isset($data['msg']) || !isset($data['status']) || !isset($data['data'])) {
            throw new Exception("返回数据格式错误", 404);
        }
        return static::json($data['msg'], $data['status'], $data['data'], $option);
    }

    /**
     * 返回成功消息，前端弹出提示
     * @param mixed $msg 成功消息
     * @param array $data 附带数据
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function success(mixed $msg = '', array $data = [])
    {
        $option['eventName'] = 'EVENT:NOTIFY';
        $option['state'] = true;
        $option['eventData'] = [
            'type' => 'success',
            'title' => '温馨提示',
            'message' => $msg,
        ];
        return static::json($msg, 0, $data, $option);
    }

    /**
     * 返回成功数据，前端不通知
     * @param mixed $data
     * @return Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function successRes(mixed $data = [], string $msg = 'success', array $option = [])
    {
        return static::json($msg, 0, $data, $option);
    }

    /**
     * 返回失败消息，前端弹出通知
     * @param mixed $msg
     * @param int $status
     * @return Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function fail(mixed $msg, int $status = 404)
    {
        return static::json($msg, $status, [], [
            'eventName' => 'EVENT:NOTIFY',
            'eventData' => [
                'type' => 'error',
                'title' => '温馨提示',
                'message' => $msg,
            ]
        ]);
    }

    /**
     * 前端踢出登录
     * @param mixed $msg
     * @param int $status
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function kickout(mixed $msg, int $status = 12000)
    {
        return static::json($msg, $status, [], [
            'eventName' => 'EVENT:KICKOUT',
            'state' => false,
            'eventData' => [
                'type' => 'error',
                'title' => '温馨提示',
                'message' => $msg,
            ],
        ]);
    }

    /**
     * 前端重定向到指定URL
     * @param string $url 重定向地址
     * @param float $delay 延迟时间(秒)
     * @param string $target 目标窗口，默认新窗口打开
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function redirect(string $url, float $delay = 0, string $target = '_self')
    {
        return static::json('success', 0, [], [
            'eventName' => 'EVENT:REDIRECT',
            'eventData' => [
                'url' => $url,
                'delay' => $delay,
                'target' => $target,
            ],
        ]);
    }

    /**
     * 前端执行路由跳转
     * @param string $path
     * @param array $routeRaw
     * @return Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function route(string $path, array $routeRaw = [])
    {
        return static::json('success', 0, [], [
            'eventName' => 'EVENT:ROUTE',
            'eventData' => array_merge([
                'path' => $path,
            ], $routeRaw),
        ]);
    }

    /**
     * 前端刷新当前页面
     * @param string $msg 通知信息，不填写则不通知
     * @param float $delay 延迟时间(秒)
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function reload(string $msg = '', float $delay = 0)
    {
        return static::json($msg, 0, [], [
            'eventName' => 'EVENT:REDIRECT',
            'eventData' => [
                'delay' => $delay,
            ],
        ]);
    }

    /**
     * 返回数据格式（仅用于模型）
     * @param mixed $model
     * @param array $option
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function successData(mixed $model, array $option = [])
    {
        if (is_array($model)) {
            $result = $model;
        } else {
            $result = $model->toArray();
        }
        $data = [
            'items' => isset($result['data']) ? $result['data'] : $result,
        ];
        if (isset($result['current_page'])) {
            $data['page'] = $result['current_page'];
        }
        $data['total'] = $result['total'] ?? 0;
        return static::json('success', 0, $data, $option);
    }
}
