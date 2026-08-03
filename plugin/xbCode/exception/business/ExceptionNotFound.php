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

/**
 * 404 Not Found 资源不存在
 *
 * 使用场景：
 * - 请求的数据记录、文件或资源在系统中不存在
 * - 接口路由不存在、API 版本已下线
 * - 关联数据被删除导致依赖关系断裂
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class ExceptionNotFound extends ExceptionBase
{
    /**
     * HTTP 状态码
     * @var int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $code = 404;

    /**
     * 事件名称
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $eventName = 'EVENT:NOTIFY';

    /**
     * 构造函数
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function init()
    {
        $this->setOption([
            'title' => '温馨提示',
            'message' => $this->message,
        ]);
    }
}
