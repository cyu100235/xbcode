<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\trait;

use Exception;
use support\Response;

/**
 * JSON 响应构造
 *
 * 前端积木云 SPA 通过响应体中的 option.eventName 派发全局事件，
 * option.state 决定 Promise 的最终态（true=resolve，false/缺省=reject），
 * 因此所有业务反馈（提示、跳转、踢出登录）都必须走 option 下发。
 */
trait XbJsonTrait
{
    /**
     * 返回JSON数据
     * @param mixed $msg
     * @param mixed $status
     * @param mixed $data
     * @param array $option
     * @return Response
     */
    protected static function json(mixed $msg, mixed $status, mixed $data = [], array $option = [])
    {
        $json = [
            'msg' => $msg,
            'status' => $status,
        ];
        if (!empty($data)) {
            $json['data'] = $data;
        }
        $json['option'] = (object) $option;
        return json($json);
    }

    /**
     * 返回固定格式JSON
     * @param array $data
     * @param array $option
     * @throws Exception
     * @return Response
     */
    protected static function response(array $data, array $option = [])
    {
        if (!isset($data['msg']) || !isset($data['status'])) {
            throw new Exception('返回数据格式错误', 404);
        }
        return static::json($data['msg'], $data['status'], $data['data'] ?? [], $option);
    }

    /**
     * 返回成功消息，前端弹出提示
     * @param mixed $msg
     * @param array $data
     * @return Response
     */
    protected static function success(mixed $msg = '', array $data = [])
    {
        return static::json($msg, 0, $data, static::notifyData($msg, 'success', true));
    }

    /**
     * 返回成功数据，前端不弹提示
     * @param mixed $data
     * @param string $msg
     * @param array $option
     * @return Response
     */
    protected static function successRes(mixed $data = [], string $msg = 'success', array $option = [])
    {
        return static::json($msg, 0, $data, $option);
    }

    /**
     * 返回分页/列表数据（适配 amis CRUD 的 items+total 结构）
     * @param mixed $model
     * @param array $option
     * @return Response
     */
    protected static function successData(mixed $model, array $option = [])
    {
        $result = is_array($model) ? $model : $model->toArray();
        $data = [
            'items' => $result['data'] ?? $result,
        ];
        if (isset($result['current_page'])) {
            $data['page'] = $result['current_page'];
        }
        if (isset($result['per_page'])) {
            $data['count'] = $result['per_page'];
        }
        $data['total'] = $result['total'] ?? count($data['items']);
        return static::json('success', 0, $data, $option);
    }

    /**
     * 返回失败消息，前端弹出提示
     * @param mixed $msg
     * @param int $status
     * @return Response
     */
    protected static function fail(mixed $msg, int $status = 404)
    {
        return static::json($msg, $status, [], static::notifyData($msg, 'error'));
    }

    /**
     * 前端踢出登录
     * @param mixed $msg
     * @param int $status
     * @return Response
     */
    protected static function kickout(mixed $msg, int $status = 401)
    {
        $option = [
            'eventName' => 'EVENT:KICKOUT',
            'state' => false,
            'eventData' => ['message' => (string) $msg],
        ];
        return static::json($msg, $status, [], $option);
    }

    /**
     * 前端重定向到指定URL
     * @param string $url
     * @param float $delay
     * @param string $target
     * @return Response
     */
    protected static function redirect(string $url, float $delay = 0, string $target = '_self')
    {
        return static::json('success', 0, [], [
            'eventName' => 'EVENT:REDIRECT',
            'state' => true,
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
     */
    protected static function route(string $path, array $routeRaw = [])
    {
        return static::json('success', 0, [], [
            'eventName' => 'EVENT:ROUTE',
            'state' => true,
            'eventData' => array_merge(['path' => $path], $routeRaw),
        ]);
    }

    /**
     * 前端刷新当前页面
     * @param string $msg
     * @param float $delay
     * @return Response
     */
    protected static function reload(string $msg = '', float $delay = 0)
    {
        $option = [
            'eventName' => 'EVENT:RELOAD',
            'state' => true,
            'eventData' => ['delay' => $delay],
        ];
        if ($msg !== '') {
            $option['eventData']['message'] = $msg;
        }
        return static::json($msg, 0, [], $option);
    }

    /**
     * 构造通知事件参数
     * @param mixed $msg
     * @param string $type
     * @param bool $state
     * @return array
     */
    protected static function notifyData(mixed $msg, string $type = 'error', bool $state = false): array
    {
        return [
            'eventName' => 'EVENT:NOTIFY',
            'state' => $state,
            'eventData' => [
                'type' => $type,
                'title' => '温馨提示',
                'message' => (string) $msg,
            ],
        ];
    }
}
