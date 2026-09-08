<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\exception\business;

use Exception;

/**
 * 业务异常基类
 *
 * 业务异常统一由 plugin\xbAdmin\exception\Handler 转换成
 * 积木云 SPA 可识别的 option 事件协议下发到前端。
 */
abstract class ExceptionBase extends Exception
{
    /**
     * 业务状态码
     * @var int
     */
    protected $code = 500;

    /**
     * 消息内容
     * @var string
     */
    protected $message = '业务处理失败';

    /**
     * 前端事件名称，取值见积木云事件总线：
     * EVENT:NOTIFY / EVENT:KICKOUT / EVENT:REDIRECT / EVENT:ROUTE / EVENT:RELOAD
     * @var string
     */
    protected $eventName = 'EVENT:NOTIFY';

    /**
     * 事件参数
     * @var array
     */
    protected $option = [];

    /**
     * 通知类型
     * @var string
     */
    protected $notifyType = 'error';

    /**
     * 构造方法
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        // 子类声明的默认文案与状态码会在父类构造时被覆盖，先行记录
        $defaultMessage = (string) $this->message;
        $defaultCode = (int) $this->code;
        parent::__construct($message, $code, $previous);
        if ($this->message === '') {
            $this->message = $defaultMessage;
        }
        if ($this->code === 0) {
            $this->code = $defaultCode;
        }
        $this->init();
    }

    /**
     * 构造初始化，补全事件参数
     * @return void
     */
    protected function init()
    {
        if (!empty($this->option) || $this->eventName === '') {
            return;
        }
        $message = $this->getMessage() !== '' ? $this->getMessage() : $this->message;
        if ($this->eventName === 'EVENT:KICKOUT') {
            $this->option = ['message' => $message];
            return;
        }
        $this->option = [
            'type' => $this->notifyType,
            'title' => '温馨提示',
            'message' => $message,
        ];
    }

    /**
     * 获取事件参数
     * @return array
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * 设置事件参数
     * @param array $option
     * @return static
     */
    public function setOption(array $option)
    {
        $this->option = $option;
        return $this;
    }

    /**
     * 获取事件名称
     * @return string
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * 获取前端事件组合参数
     * @return array
     */
    public function getEventData()
    {
        return [
            'eventName' => $this->eventName,
            'eventData' => $this->option,
        ];
    }
}
