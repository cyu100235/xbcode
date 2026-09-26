<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbAdmin\api;

use Exception;
use plugin\xbAdmin\enum\YesEnum;
use plugin\xbAdmin\enum\StateEnum;
use plugin\xbCode\base\BasePlugin;
use plugin\xbAdmin\app\model\Admin;
use plugin\xbAdmin\app\model\AdminRole;

/**
 * 插件安装
 * @copyright 贵州云铺网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class Install extends BasePlugin
{
    /**
     * 超级管理员角色名称
     * @var string
     */
    protected const SUPER_ROLE_TITLE = '超级管理员';

    /**
     * 默认超级管理员账号
     * @var array
     */
    protected const SUPER_ADMIN = [
        'username' => 'admin',
        'password' => '123456',
        'nickname' => self::SUPER_ROLE_TITLE,
    ];

    /**
     * 写入初始数据
     * @param string $version
     * @param array|null $context
     * @return void
     */
    protected static function afterInstall(string $version, array|null &$context = null)
    {
        // 创建管理员角色
        $roleId = static::createSuperRole();
        // 创建管理员账号
        static::createSuperAdmin($roleId);
    }

    /**
     * 创建超级管理员角色，拥有全部权限
     * @throws Exception
     * @return int 角色ID
     */
    protected static function createSuperRole(): int
    {
        $data = [
            'title' => self::SUPER_ROLE_TITLE,
            'is_system' => YesEnum::YES['value'],
        ];
        $model = new AdminRole;
        if (!$model->save($data)) {
            throw new Exception('创建超级管理员角色失败');
        }
        return (int) $model->id;
    }

    /**
     * 创建默认超级管理员账号
     * @param int $roleId 角色ID
     * @throws Exception
     * @return int 管理员ID
     */
    protected static function createSuperAdmin(int $roleId): int
    {
        $model = new Admin;
        $data = [
            'admin_id' => 0,
            'role_id' => $roleId,
            'username' => static::SUPER_ADMIN['username'],
            'password' => static::SUPER_ADMIN['password'],
            'nickname' => static::SUPER_ADMIN['nickname'],
            'state' => StateEnum::ENABLED['value'],
            'is_system' => YesEnum::YES['value'],
        ];
        if (!$model->save($data)) {
            throw new Exception('超级管理员账号创建失败');
        }
        return (int) $model->id;
    }
}
