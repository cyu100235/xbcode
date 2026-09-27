<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\app\model;

use plugin\xbCode\app\Model;

/**
 * 储存引擎模型
 *
 * 每行对应一个储存引擎（如本地存储、各云对象存储），name 为引擎标识，
 * plugin 记录引擎实现所在的插件，config 等具体参数存于 xb_config 的 upload 分组。
 */
class UploadEngine extends Model
{
}