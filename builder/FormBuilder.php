<?php

namespace plugin\xbCode\builder;

use plugin\xbCode\builder\form\AttrTrait;
use plugin\xbCode\builder\form\ControlTrait;
use plugin\xbCode\builder\form\DividerTrait;
use plugin\xbCode\builder\form\FormValidateTrait;
use plugin\xbCode\builder\form\RowTrait;
use plugin\xbCode\builder\form\TabsFormTrait;
use plugin\xbCode\Model;
use FormBuilder\Form;

/**
 * 表单构造器
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class FormBuilder extends Form
{
    // 表单配置属性
    use AttrTrait;
    // 虚线分割线
    use DividerTrait;
    // 表单验证
    use FormValidateTrait;
    // 表单行
    use RowTrait;
    // 表单联动
    use ControlTrait;
    // 选项卡
    use TabsFormTrait;

    /**
     * 表单对象
     * @var Form
     */
    private $builder;

    // 选项卡对象
    private $tabBuilder;
    // 表单数据规则
    private $data;
    // 请求对象
    private $request;

    // 提交后重定向地址
    protected $redirect = '';

    // 额外配置
    protected $extraConfig = [
        'submitBtn' => [
            'type' => 'primary',
            'content' => '提交',
            'show' => true,
            'style' => [
                'padding' => '0 60px'
            ]
        ],
        'resetBtn' => [
            'type' => '',
            'content' => '取消',
            'show' => true,
            'style' => [
                'padding' => '0 60px'
            ]
        ]
    ];

    /**
     * 构造函数
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function __construct()
    {
        $url = request()->url();
        $path = parse_url($url, PHP_URL_PATH);
        $rule = [];
        $config = [];
        $this->builder = Form::elm($path, $rule, $config);
    }

    /**
     * 设置表单渲染数据
     * @param array $data
     * @return FormBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-04-29
     */
    public function setFormData(array $data): FormBuilder
    {
        $this->builder->formData($data);
        return $this;
    }

    /**
     * 设置请求方式
     * @param mixed $method
     * @return FormBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-04-29
     */
    public function setMethod($method = 'GET'): FormBuilder
    {
        $this->builder->setMethod(strtoupper($method));
        return $this;
    }

    /**
     * 设置请求地址
     * @param mixed $action
     * @return FormBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-04-29
     */
    public function setAction($action = ''): FormBuilder
    {
        $this->builder->setAction($action);
        return $this;
    }

    /**
     * 设置提交后重定向地址
     * @param string $url
     * @return FormBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-04-29
     */
    public function setRedirect(string $url)
    {
        $this->redirect = $url;
        return $this;
    }
    
    /**
     * 设置数据
     * @param mixed $model
     * @return FormBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function setData(mixed $model): FormBuilder
    {
        if (!is_array($model)) {
            $data = $model->toArray();
            $this->builder->formData($data);
        }
        return $this;
    }

    /**
     * 快速生成表单
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-04-29
     */
    public function create(): array
    {
        $apiUrl = $this->builder->getAction();
        $method = $this->builder->getMethod();
        $this->data['http']['api'] = $apiUrl;
        $this->data['http']['method'] = $method;
        $this->data['config'] = $this->builder->formConfig();
        $this->data['extraConfig'] = $this->extraConfig;
        $this->data['formRule'] = $this->builder->formRule();
        $this->data['redirect'] = $this->redirect;
        return $this->data;
    }

    /**
     * 获取builder生成类对象
     * @return Form
     * @copyright 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-04-29
     */
    public function getBuilder(): Form
    {
        return $this->builder;
    }
}