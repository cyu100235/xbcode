<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\exception\business;

use Exception;

/**
 * 基类业务异常
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
abstract class ExceptionBase extends Exception
{
    /**
     * 业务状态码
     * @var int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $code = 500;

    /**
     * 事件名称
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $eventName = '';

    /**
     * 事件参数
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $option = [];

    public function __construct(string $message = "", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->init();
    }

    /**
     * 构造初始化
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function init()
    {
    }

    /**
     * 事件参数
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getOption()
    {
        return $this->option;
    }
    
    /**
     * 设置事件参数
     * @param array $option
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setOption($option)
    {
        $this->option = $option;
    }

    /**
     * 事件名称
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * 获取事件组合参数
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getEventData()
    {
        return [
            'eventName' => $this->eventName,
            'eventData' => $this->option
        ];
    }
}
