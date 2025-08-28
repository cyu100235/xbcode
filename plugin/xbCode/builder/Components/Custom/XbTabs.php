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
namespace plugin\xbCode\builder\Components\Custom;

use plugin\xbCode\builder\Components\BaseSchema;

/**
 * 自定义选项卡组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link http://www.xbcode.net
 * @method $this active(string $value) 默认选中选项卡
 * @method $this field(string $value) 选中字段
 * @method $this count(string $num) 选项卡数量
 * @method $this items(array $value) 选项卡列表
 */
class XbTabs extends BaseSchema
{
    public string $type = 'xbTabs';
    public int $count = 10;
}
