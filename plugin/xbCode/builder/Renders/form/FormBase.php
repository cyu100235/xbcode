<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Renders\form;

/**
 * 表单基础处理
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait FormBase
{
    /**
     * API保存接口地址
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $saveApi;

    /**
     * 保存数据请求类型
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $saveMethod = 'get';

    /**
     * 设置提交接口API
     * @param string $api
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setSaveApi(string $api)
    {
        $this->saveApi = $api;
        return $this;
    }

    /**
     * 获取提交接口API
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getSaveApi()
    {
        return $this->saveApi;
    }
    
    /**
     * 设置提交请求方法
     * @param string $method
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setSaveMethod(string $method = 'get')
    {
        $method = strtolower($method);
        if (!in_array($method, ['get', 'post', 'put', 'delete'])) {
            $method = 'get';
        }
        $this->saveMethod = $method;
        return $this;
    }

    /**
     * 获取请求方法
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getSaveMethod()
    {
        return $this->saveMethod;
    }
}
