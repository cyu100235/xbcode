<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @version  1.0
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Renders\Form\base;

use plugin\xbCode\builder\Components\Form\AmisForm;

/**
 * 表单数据处理
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait FormData
{
    /**
     * 表单实例
     * @var AmisForm
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected AmisForm $form;

    /**
     * API接口地址
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $api;

    /**
     * 请求方法
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $method = 'get';

    /**
     * 表单数据
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $data = [];

    /**
     * 设置表单数据
     * @param mixed $data
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setData(mixed $model)
    {
        if(!is_array($model)) {
            $data = $model->toArray();
        }else{
            $data = $model;
        }
        if($data){
            $this->form->data($data);            
        }
        return $this;
    }

    /**
     * 设置接口API
     * @param string $api
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setApi(string $api)
    {
        $this->api = $api;
        $this->buildApiData();
    }
    
    /**
     * 设置请求方法
     * @param string $method
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setMethod(string $method = 'get')
    {
        $method = strtolower($method);
        if (!in_array($method, ['get', 'post', 'put', 'delete'])) {
            $method = 'get';
        }
        $this->method = $method;
        $this->buildApiData();
        return $this;
    }

    /**
     * 获取请求方法
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getMethod()
    {
        return $this->method;
    }
    
    /**
     * 绑定API数据
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function buildApiData()
    {
        $this->form->api([
            'url' => $this->api,
            'method' => $this->method,
        ]);
    }
}
