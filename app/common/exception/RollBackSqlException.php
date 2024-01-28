<?php

namespace app\common\exception;

use Exception;
use think\facade\Log;

/**
 * 数据回滚专用异常类
 * @author 贵州小白基地网络科技有限公司
 * @copyright (c) 贵州小白基地网络科技有限公司
 */
class RollBackSqlException extends Exception
{
    /**
     * 构造函数
     * @param string $message
     * @param mixed $code
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function __construct(string $message,mixed $code = 404)
    {
        $content = "{$message}，line：{$this->getLine()}，file：{$this->getFile()}";
        Log::write($content,"xbase_rollback");
        parent::__construct($message, $code);
    }
}