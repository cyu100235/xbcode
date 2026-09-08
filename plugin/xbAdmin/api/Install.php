<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\api;

use Exception;
use Throwable;
use support\think\Db;
use plugin\xbAdmin\enum\YesEnum;
use plugin\xbAdmin\enum\StateEnum;
use plugin\xbAdmin\app\model\Admin;
use plugin\xbAdmin\app\model\AdminRole;
use plugin\xbAdmin\app\model\AdminRule;
use plugin\xbCode\api\Mysql;
use plugin\xbCode\base\BasePlugin;

/**
 * 插件安装器
 *
 * 安装顺序：建表 -> 同步菜单 -> 创建超级管理员角色 -> 创建超级管理员账号
 *
 * 幂等说明：
 *   1. 数据表已存在时跳过对应的建表语句，不会 DROP、不会清空数据；
 *   2. 菜单按「插件标识 + 权限地址」upsert，重复安装只更新不新增；
 *   3. 角色与账号仅在缺失时创建，已存在则保持原样。
 *
 * 执行方式：php webman xb:install  或  php webman app-plugin:install xbAdmin
 */
class Install extends BasePlugin
{
    /**
     * 插件标识
     * @var string
     */
    protected const PLUGIN = 'xbAdmin';

    /**
     * install.sql 使用的模板表前缀
     * @var string
     */
    protected const SQL_PREFIX = 'xb_';

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
        'nickname' => '超级管理员',
    ];

    /**
     * 安装之前，检查运行环境
     * @param string $version
     * @param array|null $context
     * @throws Exception
     * @return void
     */
    protected static function beforeInstall(string $version, array|null &$context = null)
    {
        // 检查数据表前缀，前缀为空时模型解析出的表名与建表脚本无法对应
        $prefix = static::prefix();
        if ($prefix === '') {
            throw new Exception('数据库表前缀为空，请先在项目根目录 .env 中配置 DB_PREFIX（例如 xb_）');
        }
        // 检查建表脚本
        if (!is_file(static::sqlFile())) {
            throw new Exception('install.sql 不存在：' . static::sqlFile());
        }
        // 检查菜单配置，避免建表后没有任何权限可同步
        if (empty((array) config('plugin.' . static::PLUGIN . '.menu', []))) {
            throw new Exception('菜单配置为空：plugin/' . static::PLUGIN . '/config/menu.php');
        }
    }

    /**
     * 安装中
     * @param string $version
     * @param array|null $context
     * @throws Exception
     * @return void
     */
    protected static function doInstall(string $version, array|null &$context = null)
    {
        // 建表
        static::createTables();
        // 同步菜单权限
        static::createMenus();
        // 创建超级管理员角色
        $roleId = static::createSuperRole();
        // 创建超级管理员账号
        static::createSuperAdmin($roleId);
    }

    /**
     * 卸载之前，清理本插件写入的菜单权限
     *
     * 出于数据安全考虑不会删除数据表，如需清表请手动执行：
     *   DROP TABLE IF EXISTS `前缀admin_rule`;
     *   DROP TABLE IF EXISTS `前缀admin_role`;
     *   DROP TABLE IF EXISTS `前缀admin`;
     * @param string $version
     * @param array $context
     * @return void
     */
    protected static function beforeUninstall(string $version, array &$context)
    {
        if (Mysql::hasTable(static::prefix() . 'admin_rule')) {
            Menus::uninstall(static::PLUGIN);
        }
    }

    /**
     * 创建数据表，已存在的表跳过
     * @throws Exception
     * @return void
     */
    protected static function createTables(): void
    {
        $sqls = static::sqlStatements(static::sqlFile());
        if (empty($sqls)) {
            throw new Exception('未从 install.sql 解析到任何建表语句');
        }
        $prefix = static::prefix();
        $pattern = '/^CREATE TABLE (?:IF NOT EXISTS )?`([^`]+)`/i';
        foreach ($sqls as $value) {
            // 与 Mysql::importSql 保持同样的前缀替换口径
            $sql = str_replace('`' . static::SQL_PREFIX, "`{$prefix}", $value);
            if (!preg_match($pattern, $sql, $match)) {
                continue;
            }
            // 表已存在则跳过，保证重复安装不会报错
            if (Mysql::hasTable($match[1])) {
                continue;
            }
            try {
                Db::execute($sql);
            } catch (Throwable $e) {
                throw new Exception('数据表创建失败：' . $match[1] . '，' . $e->getMessage());
            }
        }
    }

    /**
     * 同步 config/menu.php 中的菜单权限
     * @throws Exception
     * @return void
     */
    protected static function createMenus(): void
    {
        $menus = (array) config('plugin.' . static::PLUGIN . '.menu', []);
        Menus::install($menus, static::PLUGIN);
    }

    /**
     * 创建超级管理员角色，拥有全部权限
     * @throws Exception
     * @return int 角色ID
     */
    protected static function createSuperRole(): int
    {
        $id = (int) AdminRole::where([
            ['title', '=', static::SUPER_ROLE_TITLE],
            ['is_system', '=', YesEnum::YES['value']],
        ])->value('id');
        if ($id > 0) {
            return $id;
        }
        $model = new AdminRole;
        $data = [
            'admin_id' => 0,
            'title' => static::SUPER_ROLE_TITLE,
            'sort' => 0,
            'is_system' => YesEnum::YES['value'],
            // 系统角色仅作展示，鉴权时超级管理员不依赖该字段
            'rule' => AdminRole::stringifyRules((array) AdminRule::column('path')),
        ];
        if (!$model->save($data)) {
            throw new Exception('超级管理员角色创建失败');
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
        $id = (int) Admin::where('username', static::SUPER_ADMIN['username'])->value('id');
        if ($id > 0) {
            return $id;
        }
        $model = new Admin;
        $data = [
            'admin_id' => 0,
            'role_id' => $roleId,
            'username' => static::SUPER_ADMIN['username'],
            // 传入明文，由模型 setPasswordAttr 修改器统一加密，避免二次加密
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

    /**
     * install.sql 文件路径
     * @return string
     */
    protected static function sqlFile(): string
    {
        return dirname(__DIR__) . '/install.sql';
    }

    /**
     * 当前环境的数据库表前缀
     * @return string
     */
    protected static function prefix(): string
    {
        $config = Mysql::getConfig();
        return (string) ($config['connections']['mysql']['prefix'] ?? '');
    }

    /**
     * 解析 install.sql 中的建表语句
     *
     * 逐行拼接与注释过滤规则与 plugin/xbCode/api/Mysql.php::importSql 保持一致，
     * 以便同一份脚本可以被两种方式复用。
     * @param string $file SQL 文件路径
     * @return array<int, string>
     */
    protected static function sqlStatements(string $file): array
    {
        $sqls = [];
        $tmp = '';
        foreach ((array) file($file) as $line) {
            $value = trim($line);
            // 跳过空行与注释行
            if ($value === '' || str_contains($value, '--') || str_contains($value, '/*')) {
                continue;
            }
            // 保留行尾换行，避免拼接后语句缺少列分隔
            $tmp .= $line;
            // 以分号结尾表示一条完整语句
            if (!str_ends_with($value, ';')) {
                continue;
            }
            $sqls[] = trim($tmp);
            $tmp = '';
        }
        return $sqls;
    }
}
