<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Renders\form\rows;

use plugin\xbCode\builder\Components\Tabs;

/**
 * 选项卡表单项
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait TabsRow
{
    /**
     * 选项卡组件
     * @var Tabs
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected Tabs $tabs;

    /**
     * 表单按钮
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $customSubmit = [
        [
            'type' => 'submit',
            'actionType' => 'submit',
            'level' => 'primary',
            'label' => '提交保存',
        ],
    ];

    /**
     * 添加选项卡
     * @param string $name
     * @param string $title
     * @param array $components
     * @param callable|array $option
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    public function addRowTab(string $name, string $title, array $components, callable|array $option = [])
    {
        // 排除重复的表单项
        $this->excludeFormRows($components);
        if (empty($this->tabs->tabs)) {
            $this->tabs->setVariable('tabs', []);
        }
        $tab = array_merge([
            'name' => $name,
            'title' => $title,
            'body' => $components
        ], $option);
        array_push($this->tabs->tabs, $tab);
        return $this;
    }

    /**
     * 获取选项卡
     * @return Tabs
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    protected function renderTabs()
    {
        $tabs = $this->tabs->tabs;
        foreach ($tabs as $key => $value) {
            if (is_object($value)) {
                $value = $value->get();
            }
            if (!empty($value['body'])) {
                foreach ($value['body'] as $k => $v) {
                    if (is_object($v)) {
                        $v = $v->get();
                    }
                    $value['body'][$k] = $v;
                }
                $value['body'] = array_merge($value['body'], $this->customSubmit);
            }
            $tabs[$key] = $value;
        }
        $this->tabs->tabs($tabs);
        return $this->tabs;
    }
}
