<?php
namespace xbcode\trait;

use support\Response;
use Exception;

/**
 * JSON构造类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait JsonTrait
{
    /**
     * 返回JSON数据
     * @param mixed $msg
     * @param mixed $code
     * @param mixed $data
     * @return Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function json(mixed $msg, mixed $code, $data = [])
    {
        $json['msg']  = $msg;
        $json['code'] = $code;
        $json['data'] = $data;
        return json($json);
    }

    /**
     * 返回固定JSON
     * @param array $data
     * @return Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function response(array $data)
    {
        if (!isset($data['msg']) || !isset($data['code']) || !isset($data['data'])) {
            throw new Exception("返回数据格式错误", 404);

        }
        return self::json($data['msg'], $data['code'], $data['data']);
    }
    
    /**
     * 返回成功消息带token
     * @param int $code
     * @param string $token
     * @param string $msg
     * @return Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function successToken(int $code, string $token, string $msg = 'success')
    {
        $data['token'] = $token;
        return self::json($msg, $code, $data);
    }

    /**
     * 返回成功消息
     * @param mixed $msg
     * @return Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function success(mixed $msg = 'success')
    {
        return self::json($msg, 200);
    }

    /**
     * 返回成功带数据
     * @param mixed $msg
     * @param mixed $data
     * @return Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function successFul(mixed $msg, mixed $data)
    {
        return self::json($msg, 200, $data);
    }

    /**
     * 返回成功结果
     * @param mixed $data
     * @return Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function successRes(mixed $data)
    {
        return self::json('success', 200, $data);
    }

    /**
     * 返回失败消息
     * @param mixed $msg
     * @return Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function fail(mixed $msg)
    {
        return self::json($msg, 404);
    }

    /**
     * 返回失败待状态码消息
     * @param mixed $msg
     * @param mixed $code
     * @return Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function failFul(mixed $msg, mixed $code)
    {
        return self::json($msg, $code);
    }
    
    /**
     * 刷新当前页面
     * @param string $msg
     * @param string $type
     * @param int $delay
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function reload(string $msg = '',string $type ='success', int $delay = 0)
    {
        return self::json($msg, 300, [
            'delay' => $delay,
            'type' => $type
        ]);
    }

    /**
     * 重定向
     * @param string $url
     * @param string $msg
     * @param string $type
     * @param int $delay
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function redirect(string $url, string $msg = '',string $type = 'success', int $delay = 0)
    {
        return self::json($msg, 301, [
            'type' => $type,
            'url' => $url,
            'delay' => $delay
        ]);
    }
}
