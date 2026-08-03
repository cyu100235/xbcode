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
 * 重定向异常
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class ExceptionRedirect extends ExceptionBase
{
    /**
     * 业务状态码
     * @var int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $code = 301;

    /**
     * 事件名称
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $eventName = 'EVENT:REDIRECT';

    /**
     * 重定向地址
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $redirect = '';

    /**
     * 重定向地址
     * @param string $url 重定向地址
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function __construct(string $url)
    {
        parent::__construct('重定向跳转...');
        $this->init();
        $this->setRedirect($url);
    }

    /**
     * 获取重定向地址
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getRedirect()
    {
        return $this->redirect;
    }

    /**
     * 设置重定向地址
     * @param mixed $redirect
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setRedirect($redirect)
    {
        $this->redirect = $redirect;
        $this->setOption([
            'url' => $this->redirect
        ]);
    }
}
