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
namespace plugin\xbCode\builder\Components;

/**
 * 导航组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/nav
 * @method $this mode(string $value) 设置导航模式，悬浮或者内联，默认内联模式
 * @method $this collapsed(bool $value) 控制导航是否缩起
 * @method $this indentSize(int $value) 层级缩进值，仅内联模式下生效
 * @method $this level(int $value) 控制导航最大展示层级数
 * @method $this defaultOpenLevel(int $value) 控制导航最大默认展开层级
 * @method $this className(string $value) 外层 Dom 的类名
 * @method $this popupClassName(string $value) 当为悬浮模式时，可自定义悬浮层样式
 * @method $this expandIcon(string $value) 自定义展开按钮
 * @method $this expandPosition(string $value) 展开按钮位置，"before"或者"after"，不设置默认在前面
 * @method $this stacked(bool $value) 设置成 false 可以以 tabs 的形式展示
 * @method $this accordion(bool $value) 是否开启手风琴模式
 * @method $this source(string $value) 可以通过变量或 API 接口动态创建导航
 * @method $this deferApi(string $value) 用来延时加载选项详情的接口，可以不配置，不配置公用 source 接口
 * @method $this itemActions(string $value) 更多操作相关配置
 * @method $this draggable(bool $value) 是否支持拖拽排序
 * @method $this dragOnSameLevel(bool $value) 仅允许同层级内拖拽
 * @method $this saveOrderApi(string $value) 保存排序的 api
 * @method $this itemBadge(string $value) 角标
 * @method $this links(array $value) 链接集合
 * @method $this overflow(array $value) 响应式收纳配置
 * @method $this searchable(bool $value) 是否开启搜索
 * @method $this searchConfig(array $value) 搜索配置
 */
class Nav extends BaseSchema
{
    public string $type = 'nav';

    /**
     * 设置导航更多操作按钮
     * @param array $actions 行为按钮
     * @param array $option 额外配置
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addNavButtons(array $actions, array $option = [])
    {
        $component = new DropdownButton;
        $component->level('link')->icon('fa fa-ellipsis-h')->buttons($actions);
        if($option) {
            $component->setVariables($option);
        }
        $this->setVariable('itemActions', $component);
        return $this;
    }
}
