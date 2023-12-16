<?php

namespace app\common;

use app\BaseController as BaseControl;
use app\common\utils\JsonUtil;

/**
 * 控制器基类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class BaseController extends BaseControl
{
    // JSON工具类
    use JsonUtil;

    /**
     * 基类初始化
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function initialize()
    {
        parent::initialize();
        # 检测是否安装
        if (!file_exists(root_path().'.env')) {
            header('location:/install/');
        }
    }
}
