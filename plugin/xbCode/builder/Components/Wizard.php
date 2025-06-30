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
 * 向导组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/wizard
 * @method $this mode(string $value) 展示模式，选择：horizontal 或者 vertical
 * @method $this api(string $value) 最后一步保存的接口
 * @method $this initApi(string $value) 初始化数据接口
 * @method $this initFetch(string $value) 初始是否拉取数据
 * @method $this initFetchOn(string $value) 初始是否拉取数据，通过表达式来配置
 * @method $this actionPrevLabel(string $value) 上一步按钮文本
 * @method $this actionNextLabel(string $value) 下一步按钮文本
 * @method $this actionNextSaveLabel(string $value) 保存并下一步按钮文本
 * @method $this actionFinishLabel(string $value) 完成按钮文本
 * @method $this className(string $value) 外层 CSS 类名
 * @method $this actionClassName(string $value) 按钮 CSS 类名
 * @method $this reload(string $value) 操作完后刷新目标对象。请填写目标组件设置的 name 值，如果填写为 window 则让当前页面整体刷新
 * @method $this redirect(string $value) 操作完后跳转
 * @method $this target(string $value) 可以把数据提交给别的组件而不是自己保存。请填写目标组件设置的 name 值，如果填写为 window 则把数据同步到地址栏上，同时依赖这些数据的组件会自动重新刷新
 * @method $this steps(array $value) 数组，配置步骤信息
 * @method $this startStep(string $value) 起始默认值，从第几步开始。可支持模版，但是只有在组件创建时渲染模版并设置当前步数，在之后组件被刷新时，当前 step 不会根据 startStep 改变
 */
class Wizard extends BaseSchema
{
    public string $type = 'wizard';
}
