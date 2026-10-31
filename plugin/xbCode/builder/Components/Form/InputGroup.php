<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Components\Form;

use plugin\xbCode\builder\Components\BaseSchema;

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

    /**
     * 组合框内非输入类控件（amis 中这些控件会被渲染为按钮/附加内容）
     * @var array
     */
    protected const ADDON_TYPES = ['icon', 'plain', 'tpl', 'button', 'submit', 'reset'];

    /**
     * JSON序列化
     *
     * input-group 自身的 required / requiredOn 只影响必填星号，不会触发内部控件的校验，
     * 序列化时把必填配置同步给组合框内的第一个输入控件，提交时才能拦截空值。
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function jsonSerialize(): array
    {
        $vars = parent::jsonSerialize();
        $required = $vars['required'] ?? false;
        $requiredOn = (string) ($vars['requiredOn'] ?? '');
        if ((!$required && $requiredOn === '') || empty($vars['body']) || !is_array($vars['body'])) {
            return $vars;
        }
        foreach ($vars['body'] as $key => $item) {
            if (is_array($item)) {
                $type = (string) ($item['type'] ?? '');
            } elseif ($item instanceof BaseSchema) {
                $type = (string) ($item->type ?? '');
            } else {
                continue;
            }
            if ($type === '' || in_array($type, self::ADDON_TYPES, true)) {
                continue;
            }
            if (is_array($item)) {
                if ($required) {
                    $vars['body'][$key]['required'] = true;
                }
                if ($requiredOn !== '') {
                    $vars['body'][$key]['requiredOn'] = $requiredOn;
                }
            } else {
                if ($required) {
                    $item->required(true);
                }
                if ($requiredOn !== '') {
                    $item->requiredOn($requiredOn);
                }
            }
            // 只需同步第一个输入控件
            break;
        }
        return $vars;
    }
}
