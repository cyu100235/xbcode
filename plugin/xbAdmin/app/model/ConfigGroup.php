<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\app\model;

use plugin\xbCode\app\Model;

/**
 * 配置分组模型
 *
 * 一条记录以 plugin + group 唯一标识，记录插件 setting 目录下的分组模板（body 为组件数组的JSON文本）。
 * 模板由插件安装时经 HTTP 上报写入，后台系统设置页只从本表读取，
 * 因此后台与插件可以分服务器部署。
 */
class ConfigGroup extends Model
{
}