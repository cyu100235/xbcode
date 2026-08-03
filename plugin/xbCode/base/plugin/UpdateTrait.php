<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\base\plugin;

use support\think\Db;
use plugin\xbCode\api\Mysql;

/**
 * 更新插件方法
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait UpdateTrait
{
    /**
     * 更新数据库
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function updateSQL()
    {
        // 数据库信息
        $database = Db::getConfig();
        $prefix = $database['connections']['mysql']['prefix'] ?? 'xb_';
        // 替换表前缀
        $oldPrefix = ['xb_', 'php_', '__PREFIX__'];
        $newPrefix = "{$prefix}";
        // SQL文件地址
        $sqlFile = base_path() . "/plugin/{$this->name}/update/{$this->version}.sql";
        if (!file_exists($sqlFile)) {
            return;
        }
        $content = file_get_contents($sqlFile);
        if (empty($content)) {
            return;
        }
        // 安装数据库
        Mysql::importSql($sqlFile, $oldPrefix, $newPrefix);
    }
}