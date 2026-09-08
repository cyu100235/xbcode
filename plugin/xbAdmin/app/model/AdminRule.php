<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\app\model;

use plugin\xbCode\app\Model;

/**
 * 后台菜单（权限规则）模型
 *
 * path 存「模块/控制器/方法」形式的权限标识，例如 admin/AdminRole/index，
 * 与 xbAdminPathInfo() 的返回值保持同一口径。
 */
class AdminRule extends Model
{
}
