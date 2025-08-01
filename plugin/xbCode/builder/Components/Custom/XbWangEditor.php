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
 * wangEditor编辑器组件
 * @copyright 贵州猿创科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link http://www.xhadmin.cn
 * @method $this options(array $config) 编辑器配置项
 */
class XbWangEditor extends BaseSchema
{
    public string $type = 'xbWangEditor';
}
