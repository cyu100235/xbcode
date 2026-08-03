<?php
/**
 * 积木云渲染器
 * @package  XbCode.0
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\app\model;

use plugin\xbCode\Model;

/**
 * 管理员角色模型
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class AdminRole extends Model
{
    // 设置JSON字段转换
    protected $json = ['rule'];
    // 设置JSON数据返回数组
    protected $jsonAssoc = true;
}
