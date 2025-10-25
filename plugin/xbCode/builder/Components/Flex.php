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
 * Flex 布局
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/flex
 * @method $this className(string $value) css类名
 * @method $this justify(string $value) 主轴对齐方式
 * @method $this alignItems(string $value) 交叉轴对齐方式
 * @method $this style(array $value) 自定义样式
 * @method $this items(array $value) 子元素
 */
class Flex extends BaseSchema
{
    public string $type = 'flex';

    public function __construct()
    {
        $this->justify('start')->alignItems('start');
    }
}
