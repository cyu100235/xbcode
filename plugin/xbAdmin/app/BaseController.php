<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbAdmin\app;

use plugin\xbAdmin\enum\YesEnum;
use plugin\xbCode\app\XbController;
use plugin\xbAdmin\app\model\Admin;
use plugin\xbAdmin\trait\XbJsonTrait;
use plugin\xbAdmin\trait\XbViewsTrait;
use plugin\xbAdmin\exception\business\ExceptionUnauthorized;

/**
 * 后台控制器基类
 * @copyright 贵州云铺网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class BaseController extends XbController
{
    use XbJsonTrait;
    use XbViewsTrait;

    /**
     * 免登录方法
     * @var array
     */
    protected $noLogin = [];

    /**
     * 免权限校验方法，所有已登录管理员均可访问
     * @var array
     */
    protected $noAuth = [];

    /**
     * 当前登录管理员ID
     * @return int
     */
    protected function adminId(): int
    {
        return (int) (request()->uid ?? 0);
    }

    /**
     * 当前登录管理员是否超级管理员
     * @return bool
     */
    protected function isSystem(): bool
    {
        $user = $this->currentUser();
        return (string) ($user['is_system'] ?? '') === YesEnum::YES['value'];
    }

    /**
     * 当前登录管理员信息
     * @return array
     */
    protected function currentUser(): array
    {
        $user = request()->user ?? [];
        return is_array($user) ? $user : [];
    }

    /**
     * 获取当前登录管理员模型
     * @throws ExceptionUnauthorized
     * @return Admin
     */
    protected function currentModel(): Admin
    {
        $model = Admin::where('id', $this->adminId())->find();
        if (!$model) {
            throw new ExceptionUnauthorized('登录信息已失效，请重新登录');
        }
        return $model;
    }

    /**
     * 按数据归属范围过滤查询
     *
     * 超级管理员可查看全部数据，普通管理员只能查看自己名下（admin_id）的数据。
     * @param mixed $query 查询对象
     * @param string $field 归属字段
     * @return mixed
     */
    protected function dataScope(mixed $query, string $field = 'admin_id'): mixed
    {
        if (!$this->isSystem()) {
            $query->where($field, $this->adminId());
        }
        return $query;
    }
}
