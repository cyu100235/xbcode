<?php

namespace app\common\builder\form;

use app\common\builder\FormBuilder;
use FormBuilder\Driver\CustomComponent;

/**
 * 虚线分割线
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait DividerTrait
{
    /**
     * 创建表单分割线
     * @param string $title
     * @param array $extra
     * @return FormBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-04-29
     */
    public function addDivider(string $title, array $extra = []): FormBuilder
    {
        // 创建自定义组件
        $component = new CustomComponent('el-divider');
        // 设置标题
        $component->appendChild($title);
        // 设置组件属性
        $component->props($extra);
        $this->builder->append($component);
        return $this;
    }

    /**
     * 创建表单子标题
     * @param mixed $title
     * @param mixed $extra
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function addTitle($title, $extra = [])
    {
        // 创建自定义组件
        $component = new CustomComponent('x-title');
        // 设置标题
        $extra['title'] = $title;
        // 设置组件属性
        $component->props($extra);
        $this->builder->append($component);
        return $this;
    }
}