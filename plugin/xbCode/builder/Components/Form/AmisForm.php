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
namespace plugin\xbCode\builder\Components\Form;

use plugin\xbCode\builder\Components\BaseSchema;

/**
 * 表单组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/index
 * @method $this name(string $value) 表单名称
 * @method $this mode(string $value) 表单模式
 * @method $this data(array $data) 表单数据
 * @method $this horizontal(array $value) 水平布局配置
 * @method $this labelAlign(string $value) 标签对齐方式
 * @method $this labelWidth(string $value) 标签宽度
 * @method $this title(string $value) 表单标题
 * @method $this submitText(string $value) 提交按钮文本
 * @method $this className(string $value) 外层 DOM 类名
 * @method $this body(string|array $value) 表单项集合
 * @method $this actions(array $value) 表单提交按钮
 * @method $this messages(array $value) 消息提示覆写
 * @method $this wrapWithPanel(bool $value) 是否使用面板包裹
 * @method $this panelClassName(string $value) 面板类名
 * @method $this api(mixed $value) 提交数据的 API
 * @method $this initApi(string|array $value) 获取初始数据的 API
 * @method $this rules(array $value) 表单组合校验规则
 * @method $this interval(int $value) 刷新时间间隔
 * @method $this silentPolling(bool $value) 刷新时是否显示加载动画
 * @method $this stopAutoRefreshWhen(string $value) 停止刷新的条件
 * @method $this initAsyncApi(string $value) 获取初始数据的轮询 API
 * @method $this initFetch(bool $value) 是否在初始化时发起请求
 * @method $this initFetchOn(string $value) 初始化时发起请求的条件
 * @method $this initFinishedField(string $value) 初始数据完成的字段名
 * @method $this initCheckInterval(int $value) 初始数据轮询请求的时间间隔
 * @method $this asyncApi(string $value) 表单提交发送保存接口，继续轮询请求该接口，返回 finished 属性为 true 结束
 * @method $this checkInterval(int $value) 轮询请求的时间间隔
 * @method $this finishedField(string $value) 结束的字段名
 * @method $this submitOnChange(bool $value) 表单修改时是否提交
 * @method $this submitOnInit(bool $value) 初始时是否提交
 * @method $this resetAfterSubmit(bool $value) 提交后是否重置表单
 * @method $this primaryField(string $value) 主键 ID
 * @method $this target(string $value) 提交目标组件或模型
 * @method $this redirect(string $value) 提交成功后跳转的页面
 * @method $this reload(string $value) 提交后刷新目标组件
 * @method $this autoFocus(bool $value) 是否自动聚焦
 * @method $this canAccessSuperData(bool $value) 是否可以访问上层数据
 * @method $this persistData(string $value) 本地缓存的唯一 key
 * @method $this persistDataKeys(array $value) 缓存的 key 列表
 * @method $this clearPersistDataAfterSubmit(bool $value) 提交成功后是否清除本地缓存
 * @method $this preventEnterSubmit(bool $value) 禁用回车提交表单
 * @method $this trimValues(bool $value) 是否修剪表单项的值
 * @method $this promptPageLeave(bool $value) 离开页面前是否弹框确认
 * @method $this columnCount(int $value) 表单项显示的列数
 * @method $this inheritData(bool $value) 是否继承上层数据
 * @method $this static(bool $value) 是否静态展示表单
 * @method $this staticClassName(string $value) 静态展示时使用的类名
 * @method $this closeDialogOnSubmit(bool $value) 提交时是否关闭弹窗
 */
class AmisForm extends BaseSchema
{
    public string $type = 'form';

    public function __construct()
    {
        $this->mode('normal');
    }
}
