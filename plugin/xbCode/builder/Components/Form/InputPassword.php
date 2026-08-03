<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Components\Form;

/**
 * 密码组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-password
 * @method $this revealPassword(bool $value) 是否展示密码显/隐按钮
 */
class InputPassword extends InputText
{
    /**
     * 密码类型
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public string $type = 'input-password';

    /**
     * 是否展示密码显/隐按钮
     * @var bool
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    public bool $revealPassword = true;
}
