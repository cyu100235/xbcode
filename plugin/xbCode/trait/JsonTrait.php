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
     * @return Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function json(mixed $msg, mixed $status, $data = [])
    {
        $json['msg'] = $msg;
        $json['status'] = $status;
        if (!empty($data)) {
            $json['data'] = $data;
        }
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
        return static::json($data['msg'], $data['status'], $data['data']);
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
        return static::json($msg, 0, $data);
    }

    /**
     * 返回成功数据，前端不通知
     * @param mixed $data
     * @return Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function successRes(mixed $data = [], string $msg = 'success')
    {
        return static::json($msg, 0, $data);
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
        return static::json($msg, $status, []);
    }
}
