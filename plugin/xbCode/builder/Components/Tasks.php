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
 * 任务组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/tasks
 * @method $this className(string $value) 外层 Dom 的 CSS 类名
 * @method $this style(string $value) 外层 Dom 的内联样式
 * @method $this tableClassName(string $value) table Dom 的类名
 * @method $this items(array $value) 任务列表
 * @method $this checkApi(mixed $value) 返回任务列表，返回的数据请参考 items。
 * @method $this submitApi(mixed $value) 提交任务使用的 API
 * @method $this reSubmitApi(mixed $value) 如果任务失败，且可以重试，提交的时候会使用此 API
 * @method $this interval(int $value) 当有任务进行中，会每隔一段时间再次检测，而时间间隔就是通过此项配置，默认 3s。
 * @method $this taskNameLabel(string $value) 任务名称列说明
 * @method $this operationLabel(string $value) 操作列说明
 * @method $this statusLabel(string $value) 状态列说明
 * @method $this remarkLabel(string $value) 备注列说明
 * @method $this btnText(string $value) 操作按钮文字
 * @method $this retryBtnText(string $value) 重试操作按钮文字
 * @method $this btnClassName(string $value) 配置容器按钮 className
 * @method $this retryBtnClassName(string $value) 配置容器重试按钮 className
 * @method $this statusLabelMap(array $value) 状态对应类名配置["label-warning", "label-info", "label-success", "label-danger", "label-default", "label-danger"]
 * @method $this statusTextMap(array $value) 状态对应的文字显示配置["未开始", "就绪", "进行中", "出错", "已完成", "出错"]
 */
class Tasks extends BaseSchema
{
    public string $type = 'tasks';
}
