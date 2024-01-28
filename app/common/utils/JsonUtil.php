<?php

namespace app\common\utils;

use Exception;
use think\Response;

trait JsonUtil
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
    public static function json(mixed $msg, mixed $code, $data = [])
    {
        $json['msg']    = $msg;
        $json['code']   = $code;
        $json['data']   = $data;
        return Response::create($json, 'json');
    }

    /**
     * 返回固定JSON
     * @param array $data
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function toData(array $data)
    {
        if (!isset($data['msg']) || !isset($data['code']) || !isset($data['data'])){
            throw new Exception("返回数据格式错误", 404);
            
        }
        return self::json($data['msg'],$data['code'],$data['data']);
    }
    
    /**
     * 返回成功消息带token
     * @param string $msg
     * @param string $token
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function successToken(string $msg,string $token)
    {
        $data['token'] = $token;
        return self::json($msg, 200, $data);
    }

    /**
     * 返回成功消息
     * @param mixed $msg
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function success(mixed $msg)
    {
        return self::json($msg, 200);
    }

    /**
     * 返回成功带数据
     * @param mixed $msg
     * @param mixed $data
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function successFul(mixed $msg, mixed $data)
    {
        return self::json($msg, 200, $data);
    }

    /**
     * 返回成功结果
     * @param mixed $data
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function successRes(mixed $data)
    {
        return self::json('success', 200, $data);
    }

    /**
     * 返回失败消息
     * @param mixed $msg
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function fail(mixed $msg)
    {
        return self::json($msg, 404);
    }

    /**
     * 返回失败待状态码消息
     * @param mixed $msg
     * @param mixed $code
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function failFul(mixed $msg, mixed $code)
    {
        return self::json($msg, $code);
    }

    /**
     * 返回失败并重定向
     * @param mixed $msg
     * @param mixed $url
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function failRedirect(mixed $msg,mixed $url)
    {
        return self::json($msg, 302, ['url' => $url]);
    }

    /**
     * 无失败直接重定向
     * @param mixed $url
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function redirect($url)
    {
        return self::json('error', 301, ['url' => $url]);
    }
}
