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
 * 日志组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/log
 * @method $this height(int $value) 展示区域高度
 * @method $this className(string $value) 外层 CSS 类名
 * @method $this autoScroll(bool $value) 是否自动滚动
 * @method $this disableColor(bool $value) 是否禁用 ansi 颜色支持
 * @method $this placeholder(string $value) 加载中的文字
 * @method $this encoding(string $value) 返回内容的字符编码
 * @method $this source(string $value) 接口
 * @method $this credentials(string $value) fetch 的 credentials 设置
 * @method $this rowHeight(int $value) 设置每行高度，将会开启虚拟渲染
 * @method $this maxLength(int $value) 最大显示行数
 * @method $this operation(array $value) 可选日志操作：['stop','restart',clear','showLineNumber','filter']
 */
class Log extends BaseSchema
{
    public string $type = 'log';
}
