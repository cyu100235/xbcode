<?php

namespace app\common\manager;

use think\Request;
use app\common\model\AdminRole;

trait AdminRoleMgr
{
    /**
     * Saas应用ID
     * @var int
     */
    protected $saas_appid = null;

    /**
     * 模型
     * @var AdminRole
     */
    protected $model = null;
    public function indexTable(Request $request)
    {
    }
    public function index(Request $request)
    {
    }
    public function add(Request $request)
    {
    }
    public function edit(Request $request)
    {
    }
    public function del(Request $request)
    {
    }
}
