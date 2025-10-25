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
 * 代码高亮组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/code
 * @method $this className(string $value) 设置外层 Dom 的类名
 * @method $this value(string $value) 代码内容
 * @method $this name(string $value) 变量名称
 * @method $this language(string $value) 语言名称
 * @method $this tabSize(int $value) tab 大小
 * @method $this editorTheme(string $value) 编辑器主题
 * @method $this wordWrap(bool $value) 是否折行
 * @method $this maxHeight(string|int $value) 最大高度
 */
class Code extends BaseSchema
{
    public string $type = 'code';
}
