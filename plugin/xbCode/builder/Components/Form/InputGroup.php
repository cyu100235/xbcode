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

/**
 * 输入框组合
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-group
 * @method $this className(string $value) 设置 CSS 类名
 * @method $this body(array $value) 设置表单项集合
 * @method $this validationConfig(array $value) 设置校验相关配置
 * @method $this errorMode(string $value) 设置错误提示风格, full整体飘红, partial仅错误元素飘红
 * @method $this delimiter(string $value) 设置单个子元素多条校验信息的分隔符
 */
class InputGroup extends FormBase
{
    public string $type = 'input-group';
}
