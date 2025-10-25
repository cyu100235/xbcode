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
 * 页面组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/page
 * @method $this title(string $value) 页面标题
 * @method $this subTitle(string $value) 页面副标题
 * @method $this remark(string $value) 标题附近会出现一个提示图标，鼠标放上去会提示该内容
 * @method $this aside(string|array $value) 往页面的边栏区域加内容
 * @method $this asideResizor(bool $value) 页面边栏区域宽度是否可调整
 * @method $this asideMinWidth(int $value) 页面边栏区域的最小宽度
 * @method $this asideMaxWidth(int $value) 页面边栏区域的最大宽度
 * @method $this asideSticky(bool $value) 用来控制边栏固定与否
 * @method $this asidePosition(string $value) 页面边栏区域的位置
 * @method $this toolbar(mixed $value) 往页面的右上角加内容，需要注意的是，当有 title 时，该区域在右上角，没有时该区域在左上角
 * @method $this body(mixed $value) 往页面的内容区域加内容
 * @method $this className(string $value) 外层 dom 类名
 * @method $this cssVars(string $value) 自定义 CSS 变量，请参考样式
 * @method $this toolbarClassName(string $value) Toolbar dom 类名
 * @method $this bodyClassName(string $value) Body dom 类名
 * @method $this asideClassName(string $value) Aside dom 类名
 * @method $this headerClassName(string $value) Header 区域 dom 类名
 * @method $this initApi(string $value) Page 用来获取初始数据的 api。返回的数据可以整个 page 级别使用
 * @method $this initFetch(bool $value) 是否起始拉取 initApi
 * @method $this initFetchOn(string $value) 是否起始拉取 initApi, 通过表达式配置
 * @method $this interval(int $value) 刷新时间(最小 1000)
 * @method $this silentPolling(bool $value) 配置刷新时是否显示加载动画
 * @method $this stopAutoRefreshWhen(string $value) 通过表达式来配置停止刷新的条件
 * @method $this pullRefresh(string $value) 下拉刷新配置（仅用于移动端）
 */
class Page extends BaseSchema
{
    public string $type = "page";
}
