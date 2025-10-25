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
namespace plugin\xbCode\builder\Renders\form;

use plugin\xbCode\builder\Components\Form\Group;
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
     * 表单数据
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $data = [];

    /**
     * 单独设置某项数据
     * @param string $field
     * @param mixed $value
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setRowValue(string $field, mixed $value)
    {
        $form = $this->formRows;
        $form = $this->checkFormData($form, $field, $value);
        return $this;
    }

    /**
     * 处理表单数据
     * @param array $form
     * @param string $field
     * @param mixed $val
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function checkFormData(array $form, string $field, mixed $val)
    {
        foreach ($form as $key => &$value) {
            if ($value instanceof Group) {
                $form[$key]->body = $this->checkFormData($value->body, $field, $val);
            } else if ($value->name === $field) {
                $form[$key]->value = $val;
            }
        }
        return $form;
    }

    /**
     * 设置表单数据
     * @param mixed $data
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setData(mixed $model)
    {
        $data = [];
        if (is_array($model)) {
            $data = $model;
        } else if($model){
            $data = $model->toArray();
        }
        if ($data) {
            $this->form->data($data);
        }
        return $this;
    }
}
