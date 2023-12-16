<?php
namespace app\admin\controller;

use app\common\BaseController;
use app\common\manager\AdminRoleMgr;
use app\common\model\AdminRole;

/**
 * 角色管理
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class AdminRoleController extends BaseController
{
    use AdminRoleMgr;

    /**
     * 模型
     * @var AdminRole
     */
    protected $model = null;

    /**
     * 初始化
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function initialize()
    {
        parent::initialize();
        $this->model = new AdminRole;
    }
}
